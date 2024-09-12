<?php

namespace App\Controller;

use App\Entity\PureUser;
use App\Form\PureUserType;
use App\Repository\PureUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user', name: 'user_')]
final class PureUserController extends AbstractController
{
    #[Route('s',name: 'index', methods: ['GET'])]
    public function index(PureUserRepository $pureUserRepository): Response
    {
        return $this->render('pure_user/index.html.twig', [
            'pure_users' => $pureUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pureUser = new PureUser();
        $form = $this->createForm(PureUserType::class, $pureUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pureUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_pure_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_user/new.html.twig', [
            'pure_user' => $pureUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PureUser $pureUser): Response
    {
        return $this->render('pure_user/show.html.twig', [
            'pure_user' => $pureUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureUser $pureUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PureUserType::class, $pureUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_user/edit.html.twig', [
            'pure_user' => $pureUser,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PureUser $pureUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pureUser->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pureUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }


}
