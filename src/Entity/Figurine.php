<?php

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Figurine
{
    // Ajoute automatiquement created_at et updated_at
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    // Titre de la figurine
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    // Prix de la figurine
    #[ORM\Column(type: 'float')]
    private ?float $prix = null;

    // Nom du fichier image de la figurine (stocké dans /public/uploads)
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    // Relation : chaque figurine appartient à un utilisateur
    #[ORM\ManyToOne(inversedBy: 'figurines')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    // === GETTERS & SETTERS ===

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }
}
