<?php

namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Entity\PureCategorie;
use App\Entity\PureCommande;
use App\Entity\PureStatut;
use App\Form\PureAnnonceType;
use App\Form\PureCommandeType;
use App\Repository\PureAnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

final class PureAnnonceController extends AbstractController
{
    #[Route('annonces', name: 'annonce_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PureAnnonceRepository $pureAnnonceRepository): Response
    {
        $annoncesApprouvees = $pureAnnonceRepository->findBy(['approuvee' => true]);

        $categories = $entityManager->getRepository(PureCategorie::class)->findAll();

        $selectedCategory = $request->query->get('category');

        $queryBuilder = $entityManager->getRepository(PureAnnonce::class)->createQueryBuilder('a')
            ->where('a.approuvee = true');

        if ($selectedCategory) {
            $queryBuilder->andWhere('a.categorie = :category')
                ->setParameter('category', $selectedCategory);
        }

        $pure_annonces = $queryBuilder->getQuery()->getResult();

        return $this->render('pure_annonce/index.html.twig', [
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'pure_annonces' => $pure_annonces,
            'annoncesApprouvees' => $annoncesApprouvees,
            'active_page' => 'annonce_index'
        ]);
    }

    #[IsGranted('ROLE_VENDEUR')]
    #[Route('/user/creer-une-annonce', name: 'annonce_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $pureAnnonce = new PureAnnonce();
        $form = $this->createForm(PureAnnonceType::class, $pureAnnonce);
        $form->handleRequest($request);

        $user = $this->getUser();
        if ($user) {
            $pureAnnonce->setPureUser($user);
        } else {
            return $this->redirectToRoute('app_login');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $brochureFile->guessExtension();

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->addFlash('error', 'Erreur: L\'extension du fichier doit être une des suivantes: ' . implode(', ', $allowedExtensions) . '.');
                    return $this->redirectToRoute('annonce_new');
                }

                if ($brochureFile->getSize() > 5000000) {
                    $this->addFlash('error', 'Le fichier est trop volumineux. La taille maximale est de 5 Mo.');
                    return $this->redirectToRoute('annonce_new');
                }

                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fileExtension;

                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    throw new \Exception('Echec lors de la tentative de sauvegarde du fichier.');
                }
                $pureAnnonce->setImage($newFilename);
            } else {
                $pureAnnonce->setImage('default-image.jpg');
            }

            $pureAnnonce->computeSlug($slugger);

            $entityManager->persist($pureAnnonce);
            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mes_annonces', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_annonce/new.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form,
        ]);
    }


    #[Route('annonce/{slug}', name: 'annonce_show', methods: ['GET', 'POST'])]
    public function show(PureAnnonce $pureAnnonce, Request $request, EntityManagerInterface $entityManager, string $slug): Response
    {
        $pureAnnonce = $entityManager->getRepository(PureAnnonce::class)->findOneBy(['slug' => $slug, 'approuvee' => true]);

        if (!$pureAnnonce) {
            $this->addFlash('error', 'Annonce non trouvée ou non approuvée');
            return $this->redirectToRoute('annonce_index');
        }
        $commande = new PureCommande();
        $commande->setPureAnnonce($pureAnnonce);

        $form = $this->createForm(PureCommandeType::class, $commande);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $commande->setPureUser($this->getUser());
            $commande->setDateCommande((new \DateTime())->format('d-m-Y H:i:s'));

            $statut = $entityManager->getRepository(PureStatut::class)->find(1);
            $commande->setStatut($statut);

            $entityManager->persist($commande);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commande a été passée avec succès !');

            return $this->redirectToRoute('annonce_show', ['slug' => $pureAnnonce->getSlug()]);
        }

        return $this->render('pure_annonce/show.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_VENDEUR')]
    #[Route('/user/annonce/{id}/edit', name: 'annonce_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');
        $currentUser = $this->getUser();
        if ($currentUser !== $pureAnnonce->getPureUser()) {
           $this->addFlash('error','Vous n\'êtes pas autorisé à modifier cette annonce.');
           return $this->redirectToRoute('dashboard_mes_annonces');
        }
        $form = $this->createForm(PureAnnonceType::class, $pureAnnonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pureAnnonce->setApprouvee(null);

            $brochureFile = $form['image']->getData();

            if ($brochureFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png'];
                $fileExtension = $brochureFile->guessExtension();

                if (!in_array($fileExtension, $allowedExtensions)) {
                    $this->addFlash('error', 'Erreur: L\'extension du fichier doit être une des suivantes: ' . implode(', ', $allowedExtensions) . '.');
                    return $this->redirectToRoute('annonce_edit', ['id' => $pureAnnonce->getId()]);
                }

                if ($brochureFile->getSize() > 10000000) {
                    $this->addFlash('error', 'Le fichier est trop volumineux. La taille maximale est de 5 Mo.');
                    return $this->redirectToRoute('annonce_edit', ['id' => $pureAnnonce->getId()]);
                }

                if ($pureAnnonce->getImage()) {
                    $oldFilePath = $this->getParameter('images_directory') . '/' . $pureAnnonce->getImage();
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);

                $safeFilename = $slugger->slug($originalFilename);

                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fileExtension;

                try {
                    $brochureFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Échec du téléchargement de l\'image.');
                    return $this->redirectToRoute('annonce_edit', ['id' => $pureAnnonce->getId()]);
                } 


                $pureAnnonce->setImage($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('dashboard_mes_annonces', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pure_annonce/edit.html.twig', [
            'pure_annonce' => $pureAnnonce,
            'form' => $form->createView(),
        ]);
    }



    #[IsGranted('ROLE_VENDEUR')]
    #[Route('/user/annonce/{id}', name: 'annonce_delete', methods: ['POST'])]
    public function delete(Request $request, PureAnnonce $pureAnnonce, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_VENDEUR');
        $currentUser = $this->getUser();
        if ($currentUser !== $pureAnnonce->getPureUser()) {
            $this->addFlash('error', 'Vous n\'êtes pas autorisé à supprimer cette annonce.');
            return $this->redirectToRoute('dashboard_mes_annonces');
        }
        if ($this->isCsrfTokenValid('delete' . $pureAnnonce->getId(), $request->get('_token'))) {

            $imageFilename = $pureAnnonce->getImage();

            if ($imageFilename) {
                $imagePath = $this->getParameter('images_directory') . '/' . $imageFilename;
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $entityManager->remove($pureAnnonce);
            $entityManager->flush();
            $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
        } else {
            // Message en cas d'échec de la validation CSRF
            $this->addFlash('error', 'Erreur lors de la suppression de l\'annonce.');
        }
        return $this->redirectToRoute('dashboard_mes_annonces', [], Response::HTTP_SEE_OTHER);
    }

}
