<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AcheteurDashboardController extends AbstractController
{
    #[Route('/acheteur/dashboard', name: 'acheteur_dashboard')]
    public function index(): Response
    {
        return $this->render('acheteur_dashboard/index.html.twig', [
            'controller_name' => 'AcheteurDashboardController',
        ]);
    }
}
