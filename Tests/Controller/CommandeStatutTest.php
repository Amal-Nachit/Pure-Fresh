<?php

namespace App\Tests\Controller;

use App\Entity\PureCommande;
use App\Entity\PureUser;
use App\Entity\PureAnnonce;
use App\Entity\PureStatut;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommandeStatutTest extends KernelTestCase
{
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->passwordHasher = $container->get('security.user_password_hasher');
    }

    public function testCreerCommande()
    {
        $user = new PureUser();
        $user->setEmail('acheteu7@yopmail.com');
        $user->setPrenom('Arthu7');
        $user->setNom('Test3');
        $user->setTelephone('0102030405');
        $user->setAdresse('4 rue de la thur');

        // Encoder le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_ACHETEUR']);

        $annonce = new PureAnnonce();
        $annonce->setNom('Test Annonce');
        $annonce->setSlug('test');
        $annonce->setDescription('un test');
        $annonce->setPrix(100);
        $annonce->setPureUser($user);
        $annonce->setDateCreation(new \DateTime());

        $statut = new PureStatut();
        $statut->setIntitule('En attente');

        $commande = new PureCommande();
        $commande->setPureUser($user);
        $commande->setPureAnnonce($annonce);
        $commande->setStatut($statut);
        $commande->setQuantite(2);
        $commande->setTotal(200);
        $commande->setDateCommande(new \DateTime('2024-10-18'));

        $this->entityManager->persist($user);
        $this->entityManager->persist($annonce);
        $this->entityManager->persist($statut);
        $this->entityManager->persist($commande);
        $this->entityManager->flush();

        $this->assertNotNull($commande->getId());
        $this->assertEquals($user, $commande->getPureUser());
        $this->assertEquals($annonce, $commande->getPureAnnonce());
        $this->assertEquals($statut, $commande->getStatut());
        $this->assertEquals(2, $commande->getQuantite());
        $this->assertEquals(200, $commande->getTotal());
    }

    // public function testMettreAJourStatutCommande()
    // {
    //     $user = new PureUser();
    //     $user->setEmail('acheteur8@yopmail.com');
    //     $user->setPrenom('Arthur8');
    //     $user->setNom('Test8');
    //     $user->setTelephone('0102030405');
    //     $user->setAdresse('4 rue de la thur');

    //     // Encoder le mot de passe
    //     $hashedPassword = $this->passwordHasher->hashPassword($user, 'password');
    //     $user->setPassword($hashedPassword);
    //     $user->setRoles(['ROLE_VENDEUR']);

    //     $annonce = new PureAnnonce();
    //     $annonce->setNom('Test Annonce');
    //     $annonce->setSlug('test');
    //     $annonce->setDescription('un test');
    //     $annonce->setPrix(100);
    //     $annonce->setPureUser($user);
    //     $annonce->setDateCreation(new \DateTime());

    //     $statutInitial = new PureStatut();
    //     $statutInitial->setIntitule('En attente');

    //     $commande = new PureCommande();
    //     $commande->setPureUser($user);
    //     $commande->setPureAnnonce($annonce);
    //     $commande->setStatut($statutInitial);
    //     $commande->setQuantite(1);
    //     $commande->setTotal(100);
    //     $commande->setDateCommande(new \DateTime('2024-10-18'));

    //     $this->entityManager->persist($user);
    //     $this->entityManager->persist($annonce);
    //     $this->entityManager->persist($statutInitial);
    //     $this->entityManager->persist($commande);
    //     $this->entityManager->flush();

    //     $nouveauStatut = new PureStatut();
    //     $nouveauStatut->setIntitule('En cours de traitement');
    //     $this->entityManager->persist($nouveauStatut);
    //     $this->entityManager->flush();

    //     $commande->setStatut($nouveauStatut);
    //     $this->entityManager->flush();

    //     $this->assertEquals($nouveauStatut, $commande->getStatut());
    //     $this->assertEquals('En cours de traitement', $commande->getStatut()->getIntitule());
    // }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
