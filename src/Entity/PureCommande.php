<?php

namespace App\Entity;

use App\Repository\PureCommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PureCommandeRepository::class)]
#[Broadcast]
class PureCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $total = null;

    #[ORM\ManyToOne(inversedBy: 'pureCommandes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PureStatut $statut = null;

    #[ORM\ManyToOne(inversedBy: 'commande')]
    private ?PureAnnonce $pureAnnonce = null;

    #[ORM\ManyToOne(inversedBy: 'commande')]
    private ?PureUser $pureUser = null;

    public function __construct()
    {
        $this->dateCommande = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(?\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;
        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getStatut(): ?PureStatut
    {
        return $this->statut;
    }

    public function setStatut(?PureStatut $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPureAnnonce(): ?PureAnnonce
    {
        return $this->pureAnnonce;
    }

    public function setPureAnnonce(?PureAnnonce $pureAnnonce): self
    {
        $this->pureAnnonce = $pureAnnonce;
        return $this;
    }

    public function getPureUser(): ?PureUser
    {
        return $this->pureUser;
    }

    public function setPureUser(?PureUser $pureUser): self
    {
        $this->pureUser = $pureUser;
        return $this;
    }
}
