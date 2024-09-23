<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Repository\PureAnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/dashboard', name: 'dashboard')]
class UserDashboardController extends AbstractController
{
    // #[IsGranted('ROLE_ACHETEUR')]
    // #[IsGranted('ROLE_VENDEUR')]
    #[Route('/', name: '')]
    public function index(): Response
{
    $user = $this->getUser();

    if ($user) {
        if (in_array('ROLE_ACHETEUR', $user->getRoles())) {
            return $this->render('dashboard/acheteur.html.twig');
        } elseif (in_array('ROLE_VENDEUR', $user->getRoles())) {
            return $this->render('dashboard/vendeur.html.twig');
        }
    }
    // Si l'utilisateur n'est pas authentifié ou n'a pas de rôle approprié
    return $this->render('home/index.html.twig');
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
                // Accéder à l'image directement depuis l'annonce
                $imageFilename = $annonce->getImage() ?? 'default.png'; // Image par défaut si aucune image n'est définie
                $imagePath = '/uploads/images/' . $imageFilename; // Ajustez le chemin selon votre configuration

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

        // Redirection si l'utilisateur n'est pas vendeur ou pas connecté
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