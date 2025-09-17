<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ce trait ajoute automatiquement deux colonnes :
 * - created_at (date de création)
 * - updated_at (date de modification)
 *
 * Doctrine va remplir ces champs grâce aux "Lifecycle Callbacks".
 */
trait Timestampable
{
    // Date de création (jamais nulle, obligatoire)
    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $created_at = null;

    // Date de mise à jour (peut rester vide au début)
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * Cette méthode est appelée automatiquement AVANT l'insertion (persist).
     * On y met la date de création.
     */
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
    }

    /**
     * Cette méthode est appelée automatiquement AVANT une mise à jour (update).
     * On y met la nouvelle date de modification.
     */
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    // --- Getters utiles si tu veux afficher les dates ---

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }
}
