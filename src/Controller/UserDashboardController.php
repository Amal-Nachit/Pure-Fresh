<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class UserDashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        if (in_array('ROLE_ACHETEUR', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('acheteur_dashboard');
        }
        if (in_array('ROLE_VENDEUR', $this->getUser()->getRoles())) {
            return $this->redirectToRoute('vendeur_dashboard');
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
