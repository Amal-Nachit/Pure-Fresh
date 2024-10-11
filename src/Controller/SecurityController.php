<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login', priority: 10)]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        // Si l'utilisateur est déjà connecté, redirigez-le
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER);
        // }

        $targetPath = $request->headers->get('referer');

        if (!$targetPath || $targetPath === $this->generateUrl('app_login', [], UrlGeneratorInterface::ABSOLUTE_URL)) {
            $targetPath = $this->generateUrl('home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $request->getSession()->set('_security.main.target_path', $targetPath);

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/user_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    #[Route(path: '/admin/connexion', name: 'admin_login')]
    public function adminLogin(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        if ($user) {
            return $this->redirectToRoute('admin_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/admin_login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/user/déconnexion', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/admin/deconnexion', name: 'admin_logout')]
    public function adminLogout(): Response
    {
        return $this->redirectToRoute('admin_login');
    }
}
