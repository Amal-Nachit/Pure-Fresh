<?php
namespace App\Controller;

use App\Entity\PureAnnonce;
use App\Repository\PureAnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(PureAnnonceRepository $pureAnnonceRepository): Response
    {
        $annonces = $pureAnnonceRepository->findAll();
        $nbAnnonces = count($annonces);
        $nbAnnoncesPubliees = 0;
        $nbAnnoncesSupprimees = 0;

        foreach ($annonces as $annonce) {
            if ($annonce->isApprouve()) {
                $nbAnnoncesPubliees++;
            } else {
                $nbAnnoncesSupprimees++;
            }
        }

        return $this->render('admin_dashboard/index.html.twig', [
            'annonces' => $annonces,
            'nbAnnonces' => $nbAnnonces,
            'nbAnnoncesPubliees' => $nbAnnoncesPubliees,
            'nbAnnoncesSupprimees' => $nbAnnoncesSupprimees,
        ]);
    }

    #[Route('/accepter/{id}', name: 'admin_annonce_approuvee')]
    public function accepter(PureAnnonce $annonce, EntityManagerInterface $entityManager): Response
    {
        // Si l'annonce n'est pas trouvée, Symfony lancera une exception NotFoundHttpException automatiquement.

        $annonce->setApprouve(true); // Utilisez la méthode setApprouve() pour modifier l'état
        $entityManager->flush();

        $this->addFlash('success', 'L\'annonce a été approuvée avec succès.');

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/refuser/{id}', name: 'admin_annonce_refusee')]
    public function refuser(PureAnnonce $annonce, EntityManagerInterface $entityManager): Response
    {
        // Si l'annonce n'est pas trouvée, Symfony lancera une exception NotFoundHttpException automatiquement.

        $entityManager->remove($annonce);
        $entityManager->flush();

        $this->addFlash('success', 'L\'annonce a été refusée et supprimée avec succès.');

        return $this->redirectToRoute('admin_dashboard');
    }
}
