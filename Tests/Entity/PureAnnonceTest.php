<?php

namespace App\Tests\Entity;

use App\Entity\PureAnnonce;
use App\Entity\PureCategorie;
use PHPUnit\Framework\TestCase;

class PureAnnonceTest extends TestCase
{
    public function testPureAnnonceMethods()
    {
        $annonce = new PureAnnonce();

        $annonce->setNom('Test produit');
        $this->assertEquals('Test produit', $annonce->getNom());

        $annonce->setDescription('Une description pour le produit');
        $this->assertEquals('Une description pour le produit', $annonce->getDescription());

        $annonce->setPrix(49.99);
        $this->assertEquals(49.99, $annonce->getPrix());
        $annonce->setImage('test-image.jpg');
        $this->assertEquals('test-image.jpg', $annonce->getImage());

        $categorie = new PureCategorie();
        $categorie->setNom('Fruits');
        $annonce->setCategorie($categorie);
        $this->assertEquals($categorie, $annonce->getCategorie());
    }
}
