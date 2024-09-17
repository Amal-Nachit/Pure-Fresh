<?php

namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Entity\PureUser;
use App\Form\PureAnnonceType;
use App\Repository\PureAnnonceRepository;
use App\Repository\PureProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PureAnnonceController extends AbstractController
{
    #[Route('/annonces', name: 'annonce_index', methods: ['GET'])]
    public function index(PureProduitRepository $produit, PureAnnonceRepository $pureAnnonceRepository): Response
    {
        $annonces = $pureAnnonceRepository->findBy(['approuve' => true]);

        // Extraire les adresses, informations des vendeurs et les images des produits
        $coordinates = [];
        $produitsImages = [];

        foreach ($annonces as $annonce) {
            if (in_array('ROLE_VENDEUR', $annonce->getPureUser()->getRoles(), true)) {
                $adresse = $annonce->getPureUser()->getAdresse();
                $coordinate = $this->getCoordinatesFromAddress($adresse);

                // Récupérer l'image du produit lié à l'annonce
                $produit = $annonce->getPureProduit();
                $produitsImages[$annonce->getId()] = $produit->getImage();
                
                if ($coordinate) {
                    $coordinates[] = [
                        'lat' => $coordinate[0],
                        'lng' => $coordinate[1],
                        'nom' => $annonce->getPureUser()->getNom() // Ajout du nom du vendeur
                    ];
                } else {
                    error_log("Failed to get coordinates for address: " . $adresse);
                }
            }
        }

        return $this->render('pure_annonce/index.html.twig', [
            'pure_annonces' => $annonces,
            'coordinates' => json_encode($coordinates), // Encoder les coordonnées en JSON pour Twig
            'produits_images' => $produitsImages // Envoyer les images des produits à Twig
        ]);
    }


    public function getCoordinatesFromAddress(string $address): ?array
    {
        $apiKey = 'AIzaSyDB7guuh8CY_MJasUE7LC5BV4eBTXWaVco'; // Remplacez par votre clé API
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $apiKey;

        // Obtenir la réponse de l'API
        $response = @file_get_contents($url);

        // Vérifier les erreurs de la requête HTTP
        if ($response === false) {
            error_log('Failed to fetch data from Google Maps API for address: ' . $address);
            return null;
        }

        // Décoder la réponse JSON
        $data = json_decode($response, true);

        // Vérifier que la réponse est correcte et contient les coordonnées
        if (is_array($data) && isset($data['status']) && $data['status'] === 'OK' && isset($data['results'][0]['geometry']['location'])) {
            $location = $data['results'][0]['geometry']['location'];
            return [$location['lat'], $location['lng']];
        }

        // Journaliser l'erreur si la réponse ne contient pas les informations attendues
        if (isset($data['status'])) {
            error_log('Google Maps API error for address ' . $address . ': ' . $data['status']);
        } else {
            error_log('Invalid JSON response from Google Maps API for address: ' . $address);
        }

        return null;
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
        if ($this->isCsrfTokenValid('delete' . $pureAnnonce->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pureAnnonce);
            $entityManager->flush();
        }

        return $this->redirectToRoute('annonce_index', [], Response::HTTP_SEE_OTHER);
    }
}
