<?php

namespace App\Controller;

use App\Entity\PureUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
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
        ]);
    }
}