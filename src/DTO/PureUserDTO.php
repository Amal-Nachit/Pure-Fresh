<?php

namespace App\DTO;

use App\Entity\PureUser;
use Symfony\Component\Validator\Constraints as Assert;

class PureUserDTO
{
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    #[Assert\NotBlank(message: 'L\'email ne doit pas être vide.')]
    private ?string $email = null;

    private ?string $password = null;

    #[Assert\NotBlank(message: 'Le prénom ne doit pas être vide.')]
    private ?string $prenom = null;

    #[Assert\NotBlank(message: 'Le nom ne doit pas être vide.')]
    private ?string $nom = null;

    #[Assert\NotBlank(message: 'Le numéro de téléphone ne doit pas être vide.')]
    #[Assert\Regex(pattern: '/^(\+33|0)[1-9](\d{2}){4}$/', message: 'Numéro de téléphone invalide.')]
    private ?string $telephone = null;

    #[Assert\NotBlank(message: 'L\'adresse ne doit pas être vide.')]
    private ?string $adresse = null;

    public static function createFromUser(PureUser $user): self
    {
        $dto = new self();
        $dto->nom = $user->getNom();
        $dto->prenom = $user->getPrenom();
        $dto->email = $user->getEmail();
        $dto->telephone = $user->getTelephone();
        $dto->adresse = $user->getAdresse();
        return $dto;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of prenom
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * Set the value of prenom
     */
    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get the value of nom
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Set the value of nom
     */
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get the value of telephone
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Set the value of telephone
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get the value of adresse
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * Set the value of adresse
     */
    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
}
