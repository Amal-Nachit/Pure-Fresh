<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VendeurDashboardController extends AbstractController
{
    #[Route('/vendeur/dashboard', name: 'app_vendeur_dashboard')]
    public function index(): Response
    {
        return $this->render('vendeur_dashboard/index.html.twig', [
            'controller_name' => 'VendeurDashboardController',
        ]);
    }
}
