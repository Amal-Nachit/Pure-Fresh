<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Form\RegistrationFormType;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/user/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new PureUser();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $user->getEmail(); // Récupérez l'email saisi dans le formulaire
            $existingUser = $entityManager->getRepository(PureUser::class)->findOneBy(['email' => $email]);

            // Vérifiez si l'email existe déjà
            if ($existingUser) {
                if (!$existingUser->isVerified()) { // Supposons que vous avez une méthode isVerified()
                    // L'email existe et l'utilisateur n'est pas vérifié
                    $this->emailVerifier->sendEmailConfirmation(
                        'app_verify_email',
                        $existingUser,
                        (new TemplatedEmail())
                            ->from(new Address('pure.fresh.website@gmail.com', 'Pure & Fresh'))
                            ->to($existingUser->getEmail())
                            ->subject('Veuillez confirmer votre email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );

                    return $this->redirectToRoute('check_your_email'); // Rediriger vers la page d'information
                } else {
                    // L'email existe déjà et l'utilisateur est vérifié
                    $this->addFlash('error', 'Cet email est déjà enregistré et vérifié. Vous pouvez vous connecter.');
                    return $this->redirectToRoute('app_login'); // Rediriger vers la page de connexion
                }
            } else {
                // Aucun utilisateur existant, procéder à l'inscription
                $selectedRole = $form->get('role')->getData();
                $user->setRoles([$selectedRole]);

                $plainPassword = $form->get('plainPassword')->getData();
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

                $entityManager->persist($user);
                $entityManager->flush();

                // Envoyer l'email de confirmation
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(new Address('pure.fresh.website@gmail.com', 'Pure & Fresh'))
                        ->to($user->getEmail())
                        ->subject('Veuillez confirmer votre email')
                        ->htmlTemplate('registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute('check_your_email');
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/user/verifier-mon-email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Essayer d'obtenir l'utilisateur, mais ne pas refuser l'accès s'il n'est pas authentifié.
        $user = $this->getUser();

        if (!$user) {
            // Si l'utilisateur n'est pas authentifié, redirigez-le vers la page de confirmation d'email.
            return $this->redirectToRoute('email_confirme');
        }

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        return $this->redirectToRoute('email_confirme');
    }


    #[Route('/user/email-confirme', name: 'email_confirme')]
    public function emailConfirme(): Response
    {
        return $this->render('registration/email_confirme.html.twig');
    }

    #[Route('/user/confirmez-votre-email', name: 'check_your_email')]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('registration/check_your_email.html.twig', [
            'controller_name' => 'RegistrationController',
            'user' => $user
        ]);
    }

    #[Route('/user/renvoyer-email-de-confirmation', name: 'resend_verification_email')]
    public function resendEmailConfirmation(Request $request, Security $security): Response
    {
        $user = $security->getUser();
        if ($user instanceof PureUser) {
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('pure.fresh.website@gmail.com', 'Pure & Fresh'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
        }
        return $this->redirectToRoute('check_your_email');
    }

}
