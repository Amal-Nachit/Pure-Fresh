<?php

namespace App\Entity;

use App\Repository\PureStatutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\UX\Turbo\Attribute\Broadcast;

#[ORM\Entity(repositoryClass: PureStatutRepository::class)]
#[Broadcast]
class PureStatut
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $intitule = null;

    /**
     * @var Collection<int, PureCommande>
     */
    #[ORM\OneToMany(targetEntity: PureCommande::class, mappedBy: 'statut')]
    private Collection $pureCommandes;

    public function __construct()
    {
        $this->pureCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }
    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): static
    {
        $this->intitule = $intitule;

        return $this;
    }

    /**
     * @return Collection<int, PureCommande>
     */
    public function getPureCommandes(): Collection
    {
        return $this->pureCommandes;
    }

    public function addPureCommande(PureCommande $pureCommande): static
    {
        if (!$this->pureCommandes->contains($pureCommande)) {
            $this->pureCommandes->add($pureCommande);
            $pureCommande->setStatut($this);
        }

        return $this;
    }

    public function removePureCommande(PureCommande $pureCommande): static
    {
        if ($this->pureCommandes->removeElement($pureCommande)) {
            // set the owning side to null (unless already changed)
            if ($pureCommande->getStatut() === $this) {
                $pureCommande->setStatut(null);
            }
        }

        return $this;
    }
}
