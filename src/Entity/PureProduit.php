<?php

namespace App\Entity;

use App\Repository\PureProduitRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PureProduitRepository::class)]
#[Broadcast]
class PureProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255)]
    private ?string $image = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private ?string $prix = null;

    #[ORM\ManyToOne(targetEntity: PureAnnonce::class, inversedBy: 'pureProduit', cascade: ['persist', 'remove'])]
    private ?PureAnnonce $annonce = null;

    #[ORM\ManyToOne(targetEntity: PureCategorie::class, inversedBy: 'pureProduit', cascade: ['persist', 'remove'])]
    private ?PureCategorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'produit')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PureUser $pureUser = null;

    // Le constructeur initialise la date de création
    public function __construct()
    {
        $this->dateCreation = new \DateTime('now', new \DateTimeZone('Europe/Paris'));    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getAnnonce(): ?PureAnnonce
    {
        return $this->annonce;
    }

    public function setAnnonce(?PureAnnonce $annonce): static
    {
        $this->annonce = $annonce;

        return $this;
    }

    public function getCategorie(): ?PureCategorie
    {
        return $this->categorie;
    }

    public function setCategorie(?PureCategorie $categorie): static
    {
        $this->categorie = $categorie;

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

