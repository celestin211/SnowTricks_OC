<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Entity\Utilisateur;
use DateTime;

interface GenericTraitInterface
{
    /**
     * Get id
     */
    public function getId(): ?int;

    /**
     * Set dateCreation
     */
    public function setDateCreation(\DateTimeInterface $dateCreation): static;

    /**
     * Get dateCreation
     */
    public function getDateCreation(): ?\DateTimeInterface;

    /**
     * Set dateModification
     */
    public function setDateModification(?\DateTimeInterface $dateModification);

    /**
     * Get dateModification
     */
    public function getDateModification(): ?\DateTimeInterface;

    /**
     * Set creePar
     */
    public function setCreePar(?Utilisateur $creePar = null): static;

    /**
     * Get creePar
     */
    public function getCreePar(): ?Utilisateur;

    /**
     * Set modifiePar
     */
    public function setModifiePar(?Utilisateur $modifiePar): static;

    /**
     * Get modifiePar
     */
    public function getModifiePar(): ?Utilisateur;
}
