<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Repository\PureAnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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