<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Repository\PureAnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard', name: 'dashboard')]
class UserDashboardController extends AbstractController
{
    // #[IsGranted('ROLE_ACHETEUR')]
    // #[IsGranted('ROLE_VENDEUR')]
    #[Route('/', name: '')]
    public function index(): Response
    {
        if (in_array('ROLE_ACHETEUR', $this->getUser()->getRoles())) {
            return $this->render('dashboard/acheteur.html.twig');
        } else if (in_array('ROLE_VENDEUR', $this->getUser()->getRoles())) {
            return $this->render('dashboard/vendeur.html.twig');
        } else {
            return $this->render('home/index.html.twig');
        }
    }

    #[Route('/mes-annonces', name: '_mes_annonces')]
    public function mesAnnonces(PureAnnonceRepository $pureAnnonceRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est bien un vendeur (ROLE_VENDEUR)
        if ($user && in_array('ROLE_VENDEUR', $user->getRoles(), true)) {
            // Récupérer les annonces de ce vendeur
            $annonces = $pureAnnonceRepository->findBy(['pureUser' => $user]);

            // Préparer les données des annonces avec les chemins d'image
            $annoncesData = [];
            foreach ($annonces as $annonce) {
                $pureProduit = $annonce->getPureProduit();
                $imageFilename = $pureProduit ? $pureProduit->getImage() : 'default.png'; // Image par défaut
                $imagePath = '/uploads/images/' . $imageFilename; // Ajustez le chemin selon votre configuration

                $annoncesData[] = [
                    'annonce' => $annonce,
                    'imagePath' => $imagePath
                ];
            }

            return $this->render('dashboard/mes_annonces.html.twig', [
                'annoncesData' => $annoncesData,
            ]);
        }

        // Redirection si l'utilisateur n'est pas autorisé
        return $this->redirectToRoute('home');
    }

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