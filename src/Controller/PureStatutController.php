<?php

namespace App\Controller;

use App\Entity\PureStatut;
use App\Form\PureStatutType;
use App\Repository\PureStatutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/pure/statut')]
final class PureStatutController extends AbstractController
{
    #[Route(name: 'app_pure_statut_index', methods: ['GET'])]
    public function index(PureStatutRepository $pureStatutRepository): Response
    {
        return $this->render('pure_statut/index.html.twig', [
            'pure_statuts' => $pureStatutRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_pure_statut_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pureStatut = new PureStatut();
        $form = $this->createForm(PureStatutType::class, $pureStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pureStatut);
            $entityManager->flush();

            return $this->redirectToRoute('app_pure_statut_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_statut/new.html.twig', [
            'pure_statut' => $pureStatut,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pure_statut_show', methods: ['GET'])]
    public function show(PureStatut $pureStatut): Response
    {
        return $this->render('pure_statut/show.html.twig', [
            'pure_statut' => $pureStatut,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pure_statut_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureStatut $pureStatut, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PureStatutType::class, $pureStatut);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_pure_statut_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_statut/edit.html.twig', [
            'pure_statut' => $pureStatut,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_pure_statut_delete', methods: ['POST'])]
    public function delete(Request $request, PureStatut $pureStatut, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pureStatut->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pureStatut);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_pure_statut_index', [], Response::HTTP_SEE_OTHER);
    }
}
