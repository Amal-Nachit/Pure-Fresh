<?php

namespace App\Controller;

use App\Entity\PureProduit;
use App\Form\PureProduitType;
use App\Repository\PureProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $pureProduit = new PureProduit();
        $form = $this->createForm(PureProduitType::class, $pureProduit);
        $form->handleRequest($request);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if ($user) {
            $pureProduit->setPureUser($user); // Associer l'utilisateur connecté au produit
        } else {
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                // Validation de l'extension
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $brochureFile->guessExtension();

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->addFlash('error', 'Erreur: L\'extension du fichier doit être une des suivantes: ' . implode(', ', $allowedExtensions) . '.');
                    return $this->redirectToRoute('produit_new');
                }

                // Limiter la taille du fichier (ex. 5MB max)
                if ($brochureFile->getSize() > 5000000) {
                    $this->addFlash('error', 'File is too large. Maximum size is 5MB.');
                    return $this->redirectToRoute('produit_new');
                }

                // 1. Récupérer le nom du fichier sans l'extension
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);

                // 2. Nettoyer le nom du fichier (supprime les caractères dangereux)
                $safeFilename = $slugger->slug($originalFilename);

                // 3. Créer un nom de fichier unique avec une extension sécurisée
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fileExtension;

                try {
                    // 4. Déplacer le fichier vers le répertoire sécurisé
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Failed to upload the image.');
                }

                // 5. Assigner le nouveau nom de fichier à l'entité
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

            // Récupérer le nom du fichier image associé au produit
            $imageFilename = $pureProduit->getImage();

            if ($imageFilename) {
                // Chemin complet vers le fichier image
                $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;

                // Vérifier si le fichier existe avant de le supprimer
                if (file_exists($imagePath)) {
                    // Supprimer le fichier du répertoire local
                    unlink($imagePath);
                }
            }

            // Supprimer le produit de la base de données
            $entityManager->remove($pureProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('produit_index', [], Response::HTTP_SEE_OTHER);
    }

}
