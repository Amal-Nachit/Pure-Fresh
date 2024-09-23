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

    #[ORM\OneToMany(mappedBy: 'categorie', targetEntity: PureAnnonce::class, cascade: ['persist', 'remove'])]
    private iterable $pureAnnonces;

    public function __construct()
    {
        $this->pureAnnonces = new ArrayCollection();
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
     * @return Collection<int, PureAnnonce>
     */
    public function getPureAnnonces(): Collection
    {
        return $this->pureAnnonces;
    }

    public function addPureAnnonce(PureAnnonce $pureAnnonce): static
    {
        if (!$this->pureAnnonces->contains($pureAnnonce)) {
            $this->pureAnnonces->add($pureAnnonce);
            $pureAnnonce->setCategorie($this);
        }

        return $this;
    }

    public function removePureAnnonce(PureAnnonce $pureAnnonce): static
    {
        if ($this->pureAnnonces->removeElement($pureAnnonce)) {
            // set the owning side to null (unless already changed)
            if ($pureAnnonce->getCategorie() === $this) {
                $pureAnnonce->setCategorie(null);
            }
        }

        return $this;
    }
}

