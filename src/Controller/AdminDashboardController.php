<?php

namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Repository\PureAnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminDashboardController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(PureAnnonceRepository $pureAnnonceRepository): Response
    {
        $annonces = $pureAnnonceRepository->findBy(['approuvee' => null]);

        $nbAnnonces = count($annonces);
        $nbAnnoncesPubliees = $pureAnnonceRepository->count(['approuvee' => true]);

        return $this->render('admin_dashboard/index.html.twig', [
            'annonces' => $annonces,
            'nbAnnoncesPubliees' => $nbAnnoncesPubliees,
            'nbAnnonces' => $nbAnnonces,
        ]);
    }
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/annonces', name: 'admin_annonces', methods: ['GET'])]
    public function annonces(EntityManagerInterface $entityManager, PureAnnonceRepository $pureAnnonceRepository): JsonResponse
    {
        $annonces = $entityManager->getRepository(PureAnnonce::class)->findAll();
        $annonces = [];

        $nbAnnoncesPubliees = $pureAnnonceRepository->count(['approuvee' => true]);

        foreach ($annonces as $annonce) {
            $annonces[] = [
                'id' => $annonce->getId(),
                'titre' => $annonce->getTitre(),
                'quantite' => $annonce->getQuantite(),
                'datePublication' => $annonce->getDatePublication()->format('Y-m-d H:i'),
                'approuve' => $annonce->isApprouve(),
            ];
        }
        $data = [
            'annonces' => $annonces,
            'nbAnnoncesPubliees' => $nbAnnoncesPubliees,
        ];
        return new JsonResponse($data);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/accepter/{id}', name: 'admin_annonce_approuvee', methods: ['POST'])]
    public function accepter(PureAnnonce $annonce, EntityManagerInterface $entityManager): JsonResponse
    {
        $annonce->setApprouvee(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true], 200);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin/refuser/{id}', name: 'admin_annonce_refusee', methods: ['POST'])]
    public function refuser(PureAnnonce $annonce, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($annonce);
        $entityManager->flush();

        return new JsonResponse(['success' => true], 200);
    }
}
