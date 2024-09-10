<?php

namespace App\Controller;

use App\Entity\PureProduit;
use App\Form\PureProduitType;
use App\Repository\PureProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/produit', name: 'produit_')]
final class PureProduitController extends AbstractController
{
    #[Route('s', name: 'index', methods: ['GET'])]
    public function index(PureProduitRepository $pureProduitRepository): Response
    {
        return $this->render('pure_produit/index.html.twig', [
            'pure_produits' => $pureProduitRepository->findAll(),
        ]);
    }

    #[Route('/creer-un-produit', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pureProduit = new PureProduit();
        $form = $this->createForm(PureProduitType::class, $pureProduit);
        $form->handleRequest($request);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if ($user) {
            $pureProduit->setPureUser($user); // Associer l'utilisateur connecté au produit
        } else {
            // Gérer l'absence d'utilisateur connecté (par exemple rediriger vers la page de login)
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();

                $brochureFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                $pureProduit->setImage($newFilename);
            }

            $entityManager->persist($pureProduit);
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_produit/new.html.twig', [
            'pure_produit' => $pureProduit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PureProduit $pureProduit): Response
    {
        return $this->render('pure_produit/show.html.twig', [
            'pure_produit' => $pureProduit,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureProduit $pureProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PureProduitType::class, $pureProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_produit/edit.html.twig', [
            'pure_produit' => $pureProduit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PureProduit $pureProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pureProduit->getId(), $request->get('_token'))) {
            $entityManager->remove($pureProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
