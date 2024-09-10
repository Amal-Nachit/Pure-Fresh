<?php

namespace App\Entity;

use App\Repository\PureCategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PureCategorieRepository::class)]
#[Broadcast]
class PureCategorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PureProduit::class, cascade: ['persist', 'remove'])]
    private iterable $pureProduits;

    public function __construct()
    {
        $this->pureProduits = new ArrayCollection();
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

    /**
     * @return Collection<int, PureProduit>
     */
    public function getPureProduits(): Collection
    {
        return $this->pureProduits;
    }

    public function addPureProduit(PureProduit $pureProduit): static
    {
        if (!$this->pureProduits->contains($pureProduit)) {
            $this->pureProduits->add($pureProduit);
            $pureProduit->setCategorie($this);
        }

        return $this;
    }

    public function removePureProduit(PureProduit $pureProduit): static
    {
        if ($this->pureProduits->removeElement($pureProduit)) {
            // set the owning side to null (unless already changed)
            if ($pureProduit->getCategorie() === $this) {
                $pureProduit->setCategorie(null);
            }
        }

        return $this;
    }
}

