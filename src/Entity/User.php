<?php

namespace App\Entity; 
//  L’espace de noms, toutes tes entités sont dans App\Entity

use App\Entity\Traits\Timestampable; 
//  On importe le trait pour gérer automatiquement created_at et updated_at

use Doctrine\ORM\Mapping as ORM;
//  On importe les annotations/attributs Doctrine pour définir la table et les colonnes

#[ORM\Entity]
//  Indique que cette classe est une entité Doctrine (elle correspondra à une table en DB)

#[ORM\HasLifecycleCallbacks] 
//  Permet d’exécuter automatiquement les méthodes du trait (PrePersist/PreUpdate)
class User
{
    use Timestampable; 
    //  Le trait injecte deux champs (created_at, updated_at) + leur logique automatique

    #[ORM\Id] 
    // Indique que c’est la clé primaire

    #[ORM\GeneratedValue] 
    //  Doctrine va générer automatiquement l’ID (auto-increment)

    #[ORM\Column(type: 'integer')] 
    //  La colonne en DB sera un INT
    private ?int $id = null;

    #[ORM\Column(length: 255)] 
    //  Colonne VARCHAR(255), obligatoire
    private ?string $prenom = null;

    #[ORM\Column(length: 255)] 
    //  Colonne VARCHAR(255), obligatoire
    private ?string $nom = null;

    #[ORM\Column(length: 180, unique: true)] 
    //  Email, unique = pas de doublons autorisés en DB
    private ?string $email = null;

    #[ORM\Column(length: 255)] 
    //  Mot de passe hashé (on ne stocke jamais le mot de passe en clair)
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)] 
    //  Colonne optionnelle, permet de stocker le nom du fichier de l’image de profil
    private ?string $profile_image = null;

    // === GETTERS & SETTERS ===
    // Les méthodes publiques pour accéder (get) ou modifier (set) les propriétés privées

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): self
    {
        $this->profile_image = $profile_image;
        return $this;
    }
}
