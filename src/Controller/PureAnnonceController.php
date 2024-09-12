<?php

namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Entity\PureUser;
use App\Form\PureAnnonceType;
use App\Repository\PureAnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PureAnnonceController extends AbstractController
{
    #[Route('/annonces', name: 'annonce_index', methods: ['GET'])]
    public function index(PureAnnonceRepository $pureAnnonceRepository): Response
    {
        // $this->denyAccessUnlessGranted('ROLE_USER');

        

        return $this->render('pure_annonce/index.html.twig', [
            'pure_annonces' => $pureAnnonceRepository->findAll(),
        ]);
    }   

    #[Route('/deposer-une-annonce', name: 'annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle annonce
        $pureAnnonce = new PureAnnonce();

        // Créer le formulaire
        $form = $this->createForm(PureAnnonceType::class, $pureAnnonce);
        $form->handleRequest($request);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser(); // Récupère l'utilisateur actuellement connecté

        if (!$user) {
            // Si aucun utilisateur n'est connecté, rediriger vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Associer l'utilisateur à l'annonce
            $pureAnnonce->setPureUser($user);

            // Persister l'annonce
            $entityManager->persist($pureAnnonce);
            $entityManager->flush();

            // Rediriger vers la liste des annonces après la création
            return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        // Rendre la vue avec le formulaire
        return $this->render('pure_annonce/new.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form->createView(),
        ]);
    }



    #[Route('details-annonce/{id}', name: 'annonce_show', methods: ['GET'])]
    public function show(PureAnnonce $pureAnnonce): Response
    {
        return $this->render('pure_annonce/show.html.twig', [
            'pure_annonce' => $pureAnnonce,
        ]);
    }

    #[Route('/editer-mon-annonce/{id}', name: 'annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $form = $this->createForm(PureAnnonceType::class, $pureAnnonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_annonce/edit.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form,
        ]);
    }

    #[Route('supprimer-mon-annonce/{id}', name: 'annonce_delete', methods: ['POST'])]
    public function delete(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pureAnnonce->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pureAnnonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
    }
}
