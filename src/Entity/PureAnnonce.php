<?php

namespace App\Entity;

use App\Repository\PureAnnonceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PureAnnonceRepository::class)]
// #[Broadcast]
class PureAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $titre = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $datePublication = null;

    #[ORM\Column(nullable: true)]
    private ?bool $approuve = null;

    #[ORM\OneToOne(mappedBy: 'annonce', cascade: ['persist'])]
    private ?PureProduit $pureProduit = null;

    #[ORM\ManyToOne(inversedBy: 'annonce')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PureUser $pureUser = null;

    public function __construct()
    {
        // Initialisation par défaut lors de la création d'une annonce
        $this->datePublication = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDatePublication(): ?\DateTimeInterface
    {
        return $this->datePublication;
    }

    public function setDatePublication(\DateTimeInterface $datePublication): static
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function isApprouve(): ?bool
    {
        return $this->approuve;
    }

    public function setApprouve(?bool $approuve): static
    {
        $this->approuve = $approuve;

        return $this;
    }

    public function getPureProduit(): ?PureProduit
    {
        return $this->pureProduit;
    }

    public function setPureProduit(?PureProduit $pureProduit): static
    {
        // Annuler l'association précédente si nécessaire
        if ($pureProduit === null && $this->pureProduit !== null) {
            $this->pureProduit->setAnnonce(null);
        }

        // Assigner la nouvelle association
        if ($pureProduit !== null && $pureProduit->getAnnonce() !== $this) {
            $pureProduit->setAnnonce($this);
        }

        $this->pureProduit = $pureProduit;

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
