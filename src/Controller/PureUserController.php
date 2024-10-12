<?php

namespace App\Controller;

use App\DTO\PureUserDTO;
use App\Entity\PureUser;
use App\Entity\ResetPasswordRequest;
use App\Form\PureUserType;
use App\Repository\PureUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PureUserController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/utilisateurs', name: 'user_index', methods: ['GET'])]
    public function index(PureUserRepository $pureUserRepository): Response
    {
        return $this->render('pure_user/index.html.twig', [
            'pure_users' => $pureUserRepository->findAll(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
#[Route('/admin/utilisateur', name: 'user_show', methods: ['POST'])]
public function show(Request $request, PureUserRepository $userRepository): Response
{
    $id = $request->request->get('id');
    $user = $userRepository->find($id);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    return $this->render('pure_user/show.html.twig', [
        'pure_user' => $user,
    ]);
}



    #[Route('/user/vendeur/modifier', name: 'user_editVendeur', methods: ['GET', 'POST'])]
    public function editVendeur(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $userMod = PureUserDTO::createFromUser($user);

        $form = $this->createForm(PureUserType::class, $userMod);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setNom($userMod->getNom());
            $user->setPrenom($userMod->getPrenom());
            $user->setTelephone($userMod->getTelephone());
            $user->setEmail($userMod->getEmail());
            $user->setAdresse($userMod->getAdresse());
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
            
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('dashboard_mon_compte', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_user/editVendeur.html.twig', [
            'pure_user' => $user,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ACHETEUR')]
    #[Route('/user/acheteur/modifier', name: 'user_editAcheteur', methods: ['GET', 'POST'])]
    public function editAcheteur(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();
        $userMod = PureUserDTO::createFromUser($user);

        $form = $this->createForm(PureUserType::class, $userMod);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $user->setNom($userMod->getNom());
            $user->setPrenom($userMod->getPrenom());
            $user->setTelephone($userMod->getTelephone());
            $user->setEmail($userMod->getEmail());
            $user->setAdresse($userMod->getAdresse());
            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vos informations ont été mises à jour avec succès.');

            return $this->redirectToRoute('dashboard', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_user/editAcheteur.html.twig', [
            'pure_user' => $userMod,
            'form' => $form,
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/utilisateur/{id}', name: 'user_delete', methods: ['POST'])]
    public function delete(int $id, Request $request, EntityManagerInterface $entityManager, PureUserRepository $pureUserRepository): Response
    {
        $pureUser = $pureUserRepository->find($id);

        if (!$pureUser) {
            $this->addFlash('error', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('user_index');
        }

        if ($this->isCsrfTokenValid('delete' . $pureUser->getId(), $request->request->get('_token'))) {
            // Suppression des autres entités liées
            $resetPasswordRequests = $entityManager->getRepository(ResetPasswordRequest::class)
                ->findBy(['user' => $pureUser]);

            foreach ($resetPasswordRequests as $request) {
                $entityManager->remove($request);
            }

            foreach ($pureUser->getAnnonce() as $annonce) {
                $entityManager->remove($annonce);
            }

            foreach ($pureUser->getCommande() as $commande) {
                $entityManager->remove($commande);
            }

            $entityManager->remove($pureUser);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        } else {
            $this->addFlash('error', 'Échec de la suppression de l\'utilisateur.');
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }


}
