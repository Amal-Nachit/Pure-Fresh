<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Form\PureStatutType;
use App\Repository\PureAnnonceRepository;
use App\Repository\PureCommandeRepository;
use App\Repository\PureStatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
#[Route('/user/dashboard', name: 'dashboard')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(): Response
{
    $user = $this->getUser();
    if ($user instanceof PureUser) {
        if ($user->getId()) {
            if (!$user->isVerified()) {
                $this->addFlash('verify_email_error', 'Veuillez confirmer votre email avant de vous connecter');
                return $this->redirectToRoute('app_logout');
            }
        }
    }
    if ($user) {
        if (in_array('ROLE_ACHETEUR', $user->getRoles())) {
            return $this->render('dashboard/acheteur.html.twig');
        } elseif (in_array('ROLE_VENDEUR', $user->getRoles())) {
            return $this->render('dashboard/vendeur.html.twig');
        }
    }
    return $this->render('home/index.html.twig');
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
                'annoncesData' => $annoncesData
            ]);
        }
        return $this->redirectToRoute('home');
    }


    #[IsGranted('ROLE_VENDEUR')]
    #[Route('/mes-ventes', name: '_mes_ventes')]
    public function mesventes(
        PureAnnonceRepository $pureAnnonceRepository,
        PureCommandeRepository $pureCommandeRepository,
        PureStatutRepository $pureStatutRepository,
        Request $request
    ): Response {
        $user = $this->getUser();
        if ($user && in_array('ROLE_VENDEUR', $user->getRoles(), true)) {
            $annonces = $pureAnnonceRepository->findBy(['pureUser' => $user]);
            $annonceIds = array_map(fn($annonce) => $annonce->getId(), $annonces);
            $ventes = $pureCommandeRepository->findBy(['pureAnnonce' => $annonceIds]);
            
            // Récupérer tous les statuts
            $statuts = $pureStatutRepository->findAll();
            
            $forms = [];
            foreach ($ventes as $vente) {
                $form = $this->createForm(PureStatutType::class, null, [
                    'statuts' => $statuts,
                ]);
                // dd($form);
                
                $form->handleRequest($request);
                if ($form->isSubmitted() && $form->isValid()) {
                    $statut = $form->get('statut')->getData();
                    $vente->setStatut($statut);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($vente);
                    $entityManager->flush();
                }

                $forms[$vente->getId()] = $form->createView();
            }

            return $this->render('dashboard/mes_ventes.html.twig', [
                'ventes' => $ventes,
                'forms' => $forms,
            ]);
        }

        return $this->redirectToRoute('home');
    }


    #[IsGranted('ROLE_ACHETEUR')]
    #[Route('/mes-achats', name: '_mes_achats')]
    public function mesachats(PureCommandeRepository $pureCommandeRepository): Response
    {
        $user = $this->getUser();
        if ($user && in_array('ROLE_ACHETEUR', $user->getRoles(), true)) {
            $achats = $pureCommandeRepository->findBy(['pureUser' => $user]);
            return $this->render('dashboard/mes_achats.html.twig', [
                'achats' => $achats
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