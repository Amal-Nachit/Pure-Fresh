<?php

namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Form\PureAnnonceType;
use App\Repository\PureAnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/annonce', name: 'annonce_')]
final class PureAnnonceController extends AbstractController
{
    #[Route('s', name: 'index', methods: ['GET'])]
    public function index(PureAnnonceRepository $pureAnnonceRepository): Response
    {
        // Récupérer les annonces approuvées uniquement
        $annoncesApprouvees = $pureAnnonceRepository->findBy(['approuvee' => true]);

        return $this->render('pure_annonce/index.html.twig', [
            'pure_annonces' => $annoncesApprouvees,
        ]);
    }


    #[Route('/creer-un-annonce', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $pureAnnonce = new PureAnnonce();
        $form = $this->createForm(PureAnnonceType::class, $pureAnnonce);
        $form->handleRequest($request);

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        if ($user) {
            $pureAnnonce->setPureUser($user);
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
                    return $this->redirectToRoute('annonce_new');
                }

                // Limiter la taille du fichier (ex. 5MB max)
                if ($brochureFile->getSize() > 5000000) {
                    $this->addFlash('error', 'File is too large. Maximum size is 5MB.');
                    return $this->redirectToRoute('annonce_new');
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
                $pureAnnonce->setImage($newFilename);
            }

            $entityManager->persist($pureAnnonce);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mes_annonces', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_annonce/new.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(PureAnnonce $pureAnnonce): Response
    {
        return $this->render('pure_annonce/show.html.twig', [
            'pure_annonce' => $pureAnnonce,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager): Response
    {
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

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $pureAnnonce->getId(), $request->get('_token'))) {

            // Récupérer le nom du fichier image associé au annonce
            $imageFilename = $pureAnnonce->getImage();

            if ($imageFilename) {
                // Chemin complet vers le fichier image
                $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;

                // Vérifier si le fichier existe avant de le supprimer
                if (file_exists($imagePath)) {
                    // Supprimer le fichier du répertoire local
                    unlink($imagePath);
                }
            }

            // Supprimer le annonce de la base de données
            $entityManager->remove($pureAnnonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
    }

}
