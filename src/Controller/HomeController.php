<?php

namespace App\Controller;

use App\Entity\PureUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'error' => $error,
            'active_page' => 'home'
        ]);
    }

    #[Route('/rgpd', name: 'rgpd')]
    public function rgpd(): Response
    {
        // Récupère l'utilisateur connecté
        $user = $this->getUser();

        return $this->render('home/rgpd.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $user,
            'active_page' => 'home'
        ]);
    }

    #[Route('/mentions-legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('home/mentions-legales.html.twig', [
            'controller_name' => 'HomeController',
            'active_page' => 'home'
        ]);
    }
}