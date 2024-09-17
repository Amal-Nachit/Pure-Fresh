<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        // Vérifier si l'utilisateur est déjà connecté
        $user = $security->getUser();
        // if ($user) {
        //     $roles = $user->getRoles();

            // if (in_array('ROLE_ADMIN', $roles, true)) {
            //     return $this->redirectToRoute('admin_dashboard');
            // }

            // if (in_array('ROLE_ACHETEUR', $roles, true)) {
            //     return $this->redirectToRoute('acheteur_dashboard');
            // }

            // if (in_array('ROLE_VENDEUR', $roles, true)) {
            //     return $this->redirectToRoute('vendeur_dashboard');
            // }

            // // Redirection par défaut si aucun rôle ne correspond
            // return $this->redirectToRoute('app_home');
        // }

        // Obtenir l'erreur de connexion s'il y en a
        $error = $authenticationUtils->getLastAuthenticationError();
        // Dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
