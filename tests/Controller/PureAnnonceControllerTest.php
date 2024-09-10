<?php

namespace App\Tests\Controller;

use App\Entity\PureAnnonce;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class PureAnnonceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/pure/annonce/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(PureAnnonce::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PureAnnonce index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'pure_annonce[titre]' => 'Testing',
            'pure_annonce[quantite]' => 'Testing',
            'pure_annonce[description]' => 'Testing',
            'pure_annonce[disponibilite]' => 'Testing',
            'pure_annonce[datePublication]' => 'Testing',
            'pure_annonce[dateExpiration]' => 'Testing',
            'pure_annonce[approuve]' => 'Testing',
            'pure_annonce[pureProduit]' => 'Testing',
            'pure_annonce[pureUser]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new PureAnnonce();
        $fixture->setTitre('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setDescription('My Title');
        $fixture->setDisponibilite('My Title');
        $fixture->setDatePublication('My Title');
        $fixture->setDateExpiration('My Title');
        $fixture->setApprouve('My Title');
        $fixture->setPureProduit('My Title');
        $fixture->setPureUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('PureAnnonce');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new PureAnnonce();
        $fixture->setTitre('Value');
        $fixture->setQuantite('Value');
        $fixture->setDescription('Value');
        $fixture->setDisponibilite('Value');
        $fixture->setDatePublication('Value');
        $fixture->setDateExpiration('Value');
        $fixture->setApprouve('Value');
        $fixture->setPureProduit('Value');
        $fixture->setPureUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'pure_annonce[titre]' => 'Something New',
            'pure_annonce[quantite]' => 'Something New',
            'pure_annonce[description]' => 'Something New',
            'pure_annonce[disponibilite]' => 'Something New',
            'pure_annonce[datePublication]' => 'Something New',
            'pure_annonce[dateExpiration]' => 'Something New',
            'pure_annonce[approuve]' => 'Something New',
            'pure_annonce[pureProduit]' => 'Something New',
            'pure_annonce[pureUser]' => 'Something New',
        ]);

        self::assertResponseRedirects('/pure/annonce/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitre());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getDisponibilite());
        self::assertSame('Something New', $fixture[0]->getDatePublication());
        self::assertSame('Something New', $fixture[0]->getDateExpiration());
        self::assertSame('Something New', $fixture[0]->getApprouve());
        self::assertSame('Something New', $fixture[0]->getPureProduit());
        self::assertSame('Something New', $fixture[0]->getPureUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new PureAnnonce();
        $fixture->setTitre('Value');
        $fixture->setQuantite('Value');
        $fixture->setDescription('Value');
        $fixture->setDisponibilite('Value');
        $fixture->setDatePublication('Value');
        $fixture->setDateExpiration('Value');
        $fixture->setApprouve('Value');
        $fixture->setPureProduit('Value');
        $fixture->setPureUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/pure/annonce/');
        self::assertSame(0, $this->repository->count([]));
    }
}
