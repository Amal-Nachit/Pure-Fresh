<?php

namespace App\Tests\Controller;

use App\Entity\PureCommande;
use App\Entity\PureUser;
use App\Entity\PureAnnonce;
use App\Entity\PureStatut;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CommandeStatutTest extends KernelTestCase
{
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->passwordHasher = $container->get(UserPasswordHasherInterface::class);
    }

    public function testCreerCommande()
    {
        $acheteurRepo = $this->entityManager->getRepository(PureUser::class);
        $acheteur = $acheteurRepo->findOneBy(['email' => 'acheteur@test.com']);

        if (!$acheteur) {
            $acheteur = new PureUser();
            $acheteur->setEmail('acheteur@test.com');
            $acheteur->setPrenom('Acheteur');
            $acheteur->setNom('Test');
            $acheteur->setTelephone('0102030405');
            $acheteur->setAdresse('4 rue de la Réussite');

            $hashedPassword = $this->passwordHasher->hashPassword($acheteur, 'password');
            $acheteur->setPassword($hashedPassword);
            $acheteur->setRoles(['ROLE_ACHETEUR']);

            $this->entityManager->persist($acheteur);
        }

        $vendeurRepo = $this->entityManager->getRepository(PureUser::class);
        $vendeur = $vendeurRepo->findOneBy(['email' => 'vendeur@test.com']);

        if (!$vendeur) {
            $vendeur = new PureUser();
            $vendeur->setEmail('vendeur@test.com');
            $vendeur->setPrenom('Vendeur');
            $vendeur->setNom('Test');
            $vendeur->setTelephone('0203040506');
            $vendeur->setAdresse('5 rue de la Victoire');

            $hashedPassword = $this->passwordHasher->hashPassword($vendeur, 'password');
            $vendeur->setPassword($hashedPassword);
            $vendeur->setRoles(['ROLE_VENDEUR']);

            $this->entityManager->persist($vendeur);
        }

        $annonceRepo = $this->entityManager->getRepository(PureAnnonce::class);
        $annonce = $annonceRepo->findOneBy(['slug' => 'test-annonce']);

        if (!$annonce) {
            $annonce = new PureAnnonce();
            $annonce->setNom('Test Annonce');
            $annonce->setSlug('test-annonce');
            $annonce->setDescription('Ceci est un test');
            $annonce->setPrix(100);
            $annonce->setPureUser($vendeur);
            $annonce->setDateCreation(new \DateTime());

            $this->entityManager->persist($annonce);
        }

        $statut = new PureStatut();
        $statut->setIntitule('En attente');

        $commande = new PureCommande();
        $commande->setPureUser($acheteur);
        $commande->setPureAnnonce($annonce);
        $commande->setStatut($statut);
        $commande->setQuantite(2);
        $commande->setTotal(200);
        $commande->setDateCommande(new \DateTime('2024-10-18'));

        $this->entityManager->persist($statut);
        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        $this->assertNotNull($commande->getId());
        $this->assertEquals($acheteur, $commande->getPureUser());
        $this->assertEquals($annonce, $commande->getPureAnnonce());
        $this->assertEquals($statut, $commande->getStatut());
        $this->assertEquals(2, $commande->getQuantite());
        $this->assertEquals(200, $commande->getTotal());
    }

    public function testMettreAJourStatutCommande()
    {
        $acheteurRepo = $this->entityManager->getRepository(PureUser::class);
        $acheteur = $acheteurRepo->findOneBy(['email' => 'acheteur@test.com']);

        $vendeurRepo = $this->entityManager->getRepository(PureUser::class);
        $vendeur = $vendeurRepo->findOneBy(['email' => 'vendeur@test.com']);

        $annonceRepo = $this->entityManager->getRepository(PureAnnonce::class);
        $annonce = $annonceRepo->findOneBy(['slug' => 'test-annonce']);

        $statutRepo = $this->entityManager->getRepository(PureStatut::class);
        $statutInitial = $statutRepo->findOneBy(['intitule' => 'En attente']);

        $commande = new PureCommande();
        $commande->setPureUser($acheteur);
        $commande->setPureAnnonce($annonce);
        $commande->setStatut($statutInitial);
        $commande->setQuantite(1);
        $commande->setTotal(100);
        $commande->setDateCommande(new \DateTime('2024-10-18'));

        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        $nouveauStatut = new PureStatut();
        $nouveauStatut->setIntitule('En cours de traitement');
        $this->entityManager->persist($nouveauStatut);
        $this->entityManager->flush();

        $commande->setStatut($nouveauStatut);
        $this->entityManager->flush();

        $this->assertEquals($nouveauStatut, $commande->getStatut());
        $this->assertEquals('En cours de traitement', $commande->getStatut()->getIntitule());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
