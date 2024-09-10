<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PureCommandeController extends AbstractController
{
    #[Route('/pure/commande', name: 'app_pure_commande')]
    public function index(): Response
    {
        return $this->render('pure_commande/index.html.twig', [
            'controller_name' => 'PureCommandeController',
        ]);
    }
}
