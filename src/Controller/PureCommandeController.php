<?php

namespace App\Controller;

use App\Entity\PureCommande;
use App\Form\PureCommandeType;
use App\Repository\PureCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/commande')]
final class PureCommandeController extends AbstractController
{
    #[Route(name: 'commande_index', methods: ['GET'])]
    public function index(PureCommandeRepository $pureCommandeRepository): Response
    {
        return $this->render('pure_commande/index.html.twig', [
            'pure_commandes' => $pureCommandeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pureCommande = new PureCommande();
        $form = $this->createForm(PureCommandeType::class, $pureCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pureCommande);
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_commande/new.html.twig', [
            'pure_commande' => $pureCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'commande_show', methods: ['GET'])]
    public function show(PureCommande $pureCommande): Response
    {
        return $this->render('pure_commande/show.html.twig', [
            'pure_commande' => $pureCommande,
        ]);
    }

    #[Route('/{id}/edit', name: 'commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureCommande $pureCommande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PureCommandeType::class, $pureCommande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_commande/edit.html.twig', [
            'pure_commande' => $pureCommande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'commande_delete', methods: ['POST'])]
    public function delete(Request $request, PureCommande $pureCommande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pureCommande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pureCommande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
