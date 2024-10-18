<?php

namespace App\Tests\Entity;

use App\Entity\PureUser;
use App\Entity\PureAnnonce;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class PureUserTest extends KernelTestCase
{
    public function testUsernamePasswordToken()
    {
        // Créer une instance de PureUser
        $pureUser = new PureUser();
        $pureUser->setEmail('test@example.com');
        $pureUser->setPassword('your_password'); // Remplacez par un mot de passe valide
        $pureUser->setRoles(['ROLE_USER', 'ROLE_VENDEUR']);

        // Créer le token
        $firewallName = 'main'; // Nom du firewall
        $token = new UsernamePasswordToken($pureUser, $firewallName, $pureUser->getRoles());

        // Vérifier que le token contient les bonnes informations
        $this->assertEquals('test@example.com', $pureUser->getUserIdentifier());
        $this->assertEquals(['ROLE_USER', 'ROLE_VENDEUR'], $token->getRoleNames());
    }

    public function testUserAnnouncements()
    {
        // Créer une instance de PureUser
        $pureUser = new PureUser();
        $pureUser->setEmail('test@example.com');
        $pureUser->setPassword('your_password');
        $pureUser->setRoles(['ROLE_VENDEUR']);

        // Créer une instance d'annonce
        $pureAnnonce = new PureAnnonce();
        $pureAnnonce->setNom('Test Annonce');
        $pureAnnonce->setDescription('Ceci est une description de test.');
        $pureAnnonce->setPrix('99.99');
        $pureAnnonce->setPureUser($pureUser); // Associer l'annonce à l'utilisateur

        // Vérifier que l'annonce est bien associée à l'utilisateur
        $this->assertSame($pureUser, $pureAnnonce->getPureUser());
    }
}