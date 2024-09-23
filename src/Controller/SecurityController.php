<?php

namespace App\Controller;

use App\Entity\PureUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(PureUser $user, AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        $user = $security->getUser();
        if ($user) {
            return $this->redirectToRoute('home');
        }
        // Obtenir l'erreur de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/user_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/admin/login', name: 'admin_login')]
    public function adminLogin(): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('admin_dashboard');
        }
        return $this->render('security/admin_login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException();
    }

    #[Route(path: '/admin/logout', name: 'admin_logout')]
    public function adminLogout(): void
    {
        throw new \LogicException();
    }
}