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

    #[ORM\Column(length: 255)]
    private ?string $dateCommande = null;

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
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): ?string
    {
        return $this->dateCommande;
    }

    public function setDateCommande(string $dateCommande): static
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        return $this;
    }
    
    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(string $total): static
    {
        $this->total = $total;

        return $this;
    }

    public function getStatut(): ?PureStatut
    {
        return $this->statut;
    }

    public function setStatut(?PureStatut $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getPureAnnonce(): ?PureAnnonce
    {
        return $this->pureAnnonce;
    }

    public function setPureAnnonce(?PureAnnonce $pureAnnonce): static
    {
        $this->pureAnnonce = $pureAnnonce;

        return $this;
    }

    public function getPureUser(): ?PureUser
    {
        return $this->pureUser;
    }

    public function setPureUser(?PureUser $pureUser): static
    {
        $this->pureUser = $pureUser;

        return $this;
    }
}
