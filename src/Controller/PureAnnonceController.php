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
        $annonces = $pureAnnonceRepository->findBy(['approuve' => true]);

        // Extraire les adresses des annonces
        $addresses = [];
        foreach ($annonces as $annonce) {
            // Extraire l'adresse de l'utilisateur uniquement s'il est vendeur
            $roles = $annonce->getPureUser()->getRoles();
            if (in_array('ROLE_VENDEUR', $roles, true)) {
                $addresses[] = $annonce->getPureUser()->getAdresse();
            }
        }

        // Convertir les adresses en coordonnées géographiques (latitude, longitude)
        $coordinates = [];
        foreach ($addresses as $address) {
            // Utilisez un service comme Google Geocoding API pour obtenir les coordonnées
            $coordinate = $this->getCoordinatesFromAddress($address);
            if ($coordinate) {
                $coordinates[] = $coordinate;
            }
        }

        // Extraire les adresses des annonces
        $addresses = [];
        foreach ($annonces as $annonce) {
            $roles = $annonce->getPureUser()->getRoles();
            if (in_array('ROLE_VENDEUR', $roles, true)) {
                $addresses[] = $annonce->getPureUser()->getAdresse();
            }
        }

        // Convertir les adresses en coordonnées géographiques (latitude, longitude)
        $coordinates = [];
        foreach ($addresses as $address) {
            $coordinate = $this->getCoordinatesFromAddress($address);
            if ($coordinate) {
                $coordinates[] = $coordinate;
            } else {
                // Log or print the address that failed to convert
                error_log("Failed to get coordinates for address: " . $address);
            }
        }

        // Extraire les informations des vendeurs pour l'affichage
        $vendeurs = [];
        foreach ($annonces as $annonce) {
            $vendeur = $annonce->getPureUser();
            if (in_array('ROLE_VENDEUR', $vendeur->getRoles(), true)) {
                $vendeurs[] = [
                    'adresse' => $vendeur->getAdresse()
                ];
            }
        }

        return $this->render('pure_annonce/index.html.twig', [
            'pure_annonces' => $annonces,
            'coordinates' => $coordinates,
            'vendeurs' => $vendeurs
        ]);

    }


    public function getCoordinatesFromAddress(string $address): ?array
    {
        $apiKey = 'AIzaSyDB7guuh8CY_MJasUE7LC5BV4eBTXWaVco';  // Remplacez par votre clé API
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($address) . '&key=' . $apiKey;

        $response = file_get_contents($url);
        $data = json_decode($response, true);

        if ($data['status'] === 'OK') {
            $location = $data['results'][0]['geometry']['location'];
            return [$location['lat'], $location['lng']];
        }

        return null; // Retourne null si la géocodage échoue
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
