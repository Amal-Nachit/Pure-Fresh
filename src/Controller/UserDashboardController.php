<?php

    namespace App\Controller;

    use App\Entity\PureAnnonce;
    use App\Entity\PureCommande;
    use App\Entity\PureUser;
    use App\Form\PureStatutType;
    use App\Repository\PureAnnonceRepository;
    use App\Repository\PureCommandeRepository;
    use App\Repository\PureStatutRepository;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\Test\FormInterface;
    use Symfony\Component\HttpFoundation\JsonResponse;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Attribute\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    #[Route('/user/dashboard', name: 'dashboard')]
    class UserDashboardController extends AbstractController
    {
        #[Route('/', name: '')]
        public function index(PureCommandeRepository $pureCommandeRepository): Response
        {
            $user = $this->getUser();

        if ($user instanceof PureUser) {
            if ($user->getId() && !$user->isVerified()) {
                $this->addFlash('verify_email_error', 'Veuillez confirmer votre email avant de vous connecter');
                return $this->redirectToRoute('app_logout');
            }

            if (in_array('ROLE_ACHETEUR', $user->getRoles())) {
                $commandes = $pureCommandeRepository->findBy(['pureUser' => $user], ['dateCommande' => 'DESC']);

                return $this->render('dashboard/acheteur.html.twig', [
                    'commandes' => $commandes,
                    'active_page' => 'dashboard'
                ]);
            } elseif (in_array('ROLE_VENDEUR', $user->getRoles())) {
                return $this->render('dashboard/vendeur.html.twig');
            }
        }

        return $this->render('home/index.html.twig');
        }

        #[Route('/commande/{id}', name: '_commande_details')]
        public function commandeDetails(PureCommande $commande): Response
        {
            // Vérifier que l'utilisateur connecté est bien le propriétaire de la commande
            if ($this->getUser() !== $commande->getPureUser()) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
            }

            return $this->render('dashboard/commande_details.html.twig', [
                'commande' => $commande,
            ]);
        }

    #[Route('/commande/{id}/modifier-quantite', name: '_modifier_quantite', methods: ['POST'])]
    public function modifierQuantite(Request $request, PureCommande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() !== $commande->getPureUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette commande.');
        }

        if ($commande->getStatut()->getIntitule() !== 'En attente') {
            throw $this->createAccessDeniedException('Vous ne pouvez plus modifier cette commande.');
        }

        $nouvelleQuantite = $request->request->getInt('quantite');
        $nouveauTotal = (float) $request->request->get('total');

        if ($nouvelleQuantite > 0) {
            $commande->setQuantite($nouvelleQuantite);
            $commande->setTotal($nouveauTotal);
            $entityManager->flush();
            $this->addFlash('success', 'La commande a été mise à jour.');
        } else {
            $this->addFlash('error', 'La quantité doit être supérieure à 0.');
        }

        return $this->redirectToRoute('dashboard');
    }


    #[IsGranted('ROLE_VENDEUR')]
        #[Route('/mes-annonces', name: '_mes_annonces')]
        public function mesAnnonces(PureAnnonceRepository $pureAnnonceRepository): Response
        {
            $user = $this->getUser();

            if ($user && in_array('ROLE_VENDEUR', $user->getRoles(), true)) {
                $annonces = $pureAnnonceRepository->findBy(['pureUser' => $user]);

                $annoncesData = [];
                foreach ($annonces as $annonce) {
                    $imageFilename = $annonce->getImage() ?? 'default.png';
                    $imagePath = '/uploads/images/' . $imageFilename;

                    $annoncesData[] = [
                        'annonce' => $annonce,
                        'imagePath' => $imagePath
                    ];
                }

                return $this->render('dashboard/mes_annonces.html.twig', [
                    'annonces' => $annonces,
                    'annoncesData' => $annoncesData,
                    'active_page' => 'dashboard/mes_annonces'
                ]);
            }
            return $this->redirectToRoute('home');
        }

        #[Route('/annonces/statut', name: '_annonces_statut', methods: ['GET'])]
        public function getAnnoncesStatuts(EntityManagerInterface $entityManager): JsonResponse
        {
            $user = $this->getUser();
            $annonces = $entityManager->getRepository(PureAnnonce::class)->findBy(['pureUser' => $user]);

            $statuts = [];
            foreach ($annonces as $annonce) {
                $statuts[$annonce->getId()] = [
                    'approuvee' => $annonce->isApprouvee(),
                    'dateCreation' => $annonce->getDateCreation()->format('d M Y')
                ];
            }
            return new JsonResponse($statuts);
        }


        #[Route('/commande/{id}/statut', name: '_commande_statut', methods: ['GET', 'POST'])]
        public function commandeStatut(
            Request $request,
            PureCommande $commande,
            EntityManagerInterface $entityManager
        ): JsonResponse {
            $user = $this->getUser();
            $isVendeur = $commande->getPureAnnonce()->getPureUser() === $user;
            $isAcheteur = $commande->getPureUser() === $user;

            if (!$isVendeur && !$isAcheteur) {
                return new JsonResponse(['error' => 'Accès non autorisé'], 403);
            }

            if ($request->isMethod('GET')) {
                return new JsonResponse([
                    'statusId' => $commande->getStatut()->getId(),
                    'statusText' => $commande->getStatut()->getIntitule(),
                    'canModify' => $isAcheteur && $commande->getStatut()->getIntitule() === 'En attente'
                ]);
            }

            if ($request->isMethod('POST') && $isVendeur) {
                $form = $this->createForm(PureStatutType::class, $commande);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager->persist($commande);
                    $entityManager->flush();

                    return new JsonResponse([
                        'success' => true,
                        'message' => 'Le statut de la commande a été mis à jour.',
                        'newStatusId' => $commande->getStatut()->getId(),
                        'newStatusText' => $commande->getStatut()->getIntitule()
                    ]);
                } else {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Une erreur est survenue lors de la mise à jour du statut.',
                        'errors' => $this->getErrorsFromForm($form) 
                    ], 400);
                }
            }

            return new JsonResponse(['error' => 'Méthode non autorisée'], 405);
        }


        private function getErrorsFromForm(FormInterface $form): array
        {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }
            foreach ($form->all() as $childForm) {
                if ($childForm instanceof FormInterface) {
                    $childErrors = $this->getErrorsFromForm($childForm);
                    if ($childErrors) {
                        $errors[$childForm->getName()] = $childErrors;
                    }
                }
            }
            return $errors;
        }


        #[IsGranted('ROLE_VENDEUR')]
        #[Route('/mes-ventes', name: '_mes_ventes')]
        public function mesventes(
            PureAnnonceRepository $pureAnnonceRepository,
            PureCommandeRepository $pureCommandeRepository,
            PureStatutRepository $pureStatutRepository,
            EntityManagerInterface $entityManager,
            Request $request
        ): Response {
            $user = $this->getUser();
            if ($user && in_array('ROLE_VENDEUR', $user->getRoles(), true)) {
                $annonces = $pureAnnonceRepository->findBy(['pureUser' => $user]);
                $annonceIds = array_map(fn($annonce) => $annonce->getId(), $annonces);
                $ventes = $pureCommandeRepository->findBy(['pureAnnonce' => $annonceIds]);

                $statuts = $pureStatutRepository->findAll();

                $forms = [];
                foreach ($ventes as $vente) {
                    $formName = 'statut_form_' . $vente->getId();
                    $form = $this->createForm(PureStatutType::class, $vente, [
                        'action' => $this->generateUrl('dashboard_commande_statut', ['id' => $vente->getId()]),
                        'method' => 'POST',
                    ]);

                    $forms[$vente->getId()] = $form->createView();
                }

                return $this->render('dashboard/mes_ventes.html.twig', [
                    'ventes' => $ventes,
                    'forms' => $forms,
                    'statuts' => $statuts
                ]);
            }

            return $this->redirectToRoute('home');
        }

        #[IsGranted('ROLE_VENDEUR')]
        #[Route('/mon-compte', name: '_mon_compte')]
        public function monCompte(PureUser $user): Response
        {
            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            return $this->render('dashboard/moncompte.html.twig', [
                'user' => $user
            ]);  
        }


    }