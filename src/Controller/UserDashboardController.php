<?php

namespace App\Controller;

use App\Repository\PureAnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard', name: 'dashboard_')]
class UserDashboardController extends AbstractController
{
    #[Route('/', name: 'user_dashboard_')]
    #[IsGranted('ROLE_ACHETEUR')]
    #[IsGranted('ROLE_VENDEUR')]
    public function index(): Response
    {
        if (in_array('ROLE_ACHETEUR', $this->getUser()->getRoles())) {
            return $this->render('acheteur_dashboard/index.html.twig');
        } else if (in_array('ROLE_VENDEUR', $this->getUser()->getRoles())) {
            return $this->render('dashboard/index.html.twig');
        } else {
            return $this->render('home/index.html.twig');
        }
    }

    #[Route('/mes-annonces', name: 'mes_annonces')]
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
}