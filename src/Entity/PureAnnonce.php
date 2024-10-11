<?php

namespace App\Entity;

use App\Repository\PureAnnonceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\Attribute\Broadcast;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PureAnnonceRepository::class)]
#[Broadcast]
class PureAnnonce
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom ne peut pas être vide")]
    #[Assert\Length(min: 3, max: 255, minMessage: "Le nom doit contenir au moins {{ limit }} caractères", maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $nom = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La description ne peut pas être vide")]
    #[Assert\Length(min: 10, minMessage: "La description doit contenir au moins {{ limit }} caractères")]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: "5M",
        mimeTypes: ["image/jpeg", "image/png", "image/gif"],
        mimeTypesMessage: "Veuillez télécharger une image valide (JPG, PNG ou GIF)."
    )]
    private ?string $image = null;


    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    #[Assert\NotBlank(message: "Le prix ne peut pas être vide")]
    #[Assert\Positive(message: "Le prix doit être positif")]
    #[Assert\LessThan(value: 1000, message: "Le prix ne peut pas dépasser 99.99")]
    private ?string $prix = null;

    #[ORM\ManyToOne(targetEntity: PureCategorie::class, inversedBy: 'pureAnnonce', cascade: ['persist'])]
    private ?PureCategorie $categorie = null;

    #[ORM\ManyToOne(inversedBy: 'pureAnnonce')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PureUser $pureUser = null;

    #[ORM\Column(nullable: true)]
    private ?bool $approuvee = null;

    /**
     * @var Collection<int, PureCommande>
     */
    #[ORM\OneToMany(targetEntity: PureCommande::class, mappedBy: 'pureAnnonce')]
    private Collection $commande;

    public function __construct()
    {
        $this->dateCreation = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->commande = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function computeSlug(SluggerInterface $slugger)
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = (string) $slugger->slug((string) $this->nom)->lower();
        }
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

    public function isApprouvee(): ?bool
    {
        return $this->approuvee;
    }

    public function setApprouvee(?bool $approuvee): static
    {
        $this->approuvee = $approuvee;

        return $this;
    }

    /**
     * @return Collection<int, PureCommande>
     */
    public function getCommande(): Collection
    {
        return $this->commande;
    }

    public function addCommande(PureCommande $commande): static
    {
        if (!$this->commande->contains($commande)) {
            $this->commande->add($commande);
            $commande->setPureAnnonce($this);
        }

        return $this;
    }

    public function removeCommande(PureCommande $commande): static
    {
        if ($this->commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getPureAnnonce() === $this) {
                $commande->setPureAnnonce(null);
            }
        }

        return $this;
    }
}

