<?php

namespace App\Entity;

use App\Repository\PureUserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: PureUserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette adresse email.')]
class PureUser implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $rgpd = null;

    /**
     * @var Collection<int, PureAnnonce>
     */
    #[ORM\OneToMany(targetEntity: PureAnnonce::class, mappedBy: 'pureUser', orphanRemoval: true)]
    private Collection $annonce;

    /**
     * @var Collection<int, PureCommande>
     */
    #[ORM\OneToMany(targetEntity: PureCommande::class, mappedBy: 'pureUser', orphanRemoval: true)]
    private Collection $commande;

    #[ORM\Column]
    private bool $isVerified = false;

    public function __construct()
    {
        $this->annonce = new ArrayCollection();
        $this->commande = new ArrayCollection();
        $this->rgpd = new \DateTime('now', new \DateTimeZone('Europe/Paris'));    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';


        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getRgpd(): ?\DateTimeInterface
    {
        return $this->rgpd;
    }

    public function setRgpd(\DateTimeInterface $rgpd): static
    {
        $this->rgpd = $rgpd;
        return $this;
    }

    /**
     * @return Collection<int, PureAnnonce>
     */
    public function getProduit(): Collection
    {
        return $this->annonce;
    }

    public function addProduit(PureAnnonce $annonce): static
    {
        if (!$this->annonce->contains($annonce)) {
            $this->annonce->add($annonce);
            $annonce->setPureUser($this);
        }

        return $this;
    }

    public function removeProduit(PureAnnonce $annonce): static
    {
        if ($this->annonce->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getPureUser() === $this) {
                $annonce->setPureUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PureAnnonce>
     */
    public function getAnnonce(): Collection
    {
        return $this->annonce;
    }

    public function addAnnonce(PureAnnonce $annonce): static
    {
        if (!$this->annonce->contains($annonce)) {
            $this->annonce->add($annonce);
            $annonce->setPureUser($this);
        }

        return $this;
    }

    public function removeAnnonce(PureAnnonce $annonce): static
    {
        if ($this->annonce->removeElement($annonce)) {
            // set the owning side to null (unless already changed)
            if ($annonce->getPureUser() === $this) {
                $annonce->setPureUser(null);
            }
        }

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
            $commande->setPureUser($this);
        }

        return $this;
    }

    public function removeCommande(PureCommande $commande): static
    {
        if ($this->commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getPureUser() === $this) {
                $commande->setPureUser(null);
            }
        }

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function serialize(): string
    {
        return serialize([$this->id, $this->email, $this->password]);
    }

    public function unserialize($serialized): void
    {
        [$this->id, $this->email, $this->password] = unserialize($serialized, ['allowed_classes' => false]);
    }
}
