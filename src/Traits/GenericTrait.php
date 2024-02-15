<?php

declare(strict_types=1);

namespace App\Traits;

use App\Entity\Utilisateur;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait GenericTrait
{
    #[ORM\Column(name: 'id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'date_creation', type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(name: 'date_modification', type: 'datetime', nullable: true)]
    protected ?\DateTimeInterface $dateModification;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'cree_par_id', referencedColumnName: 'id', nullable: true)]
    protected ?Utilisateur $creePar;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(name: 'modifie_par_id', referencedColumnName: 'id', nullable: true)]
    protected ?Utilisateur $modifiePar;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setDateCreation(?\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateModification(?\DateTimeInterface $dateModification): static
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->dateModification;
    }

    public function setCreePar(?Utilisateur $creePar = null): static
    {
        $this->creePar = $creePar;

        return $this;
    }

    public function getCreePar(): ?Utilisateur
    {
        return $this->creePar;
    }

    public function setModifiePar(?Utilisateur $modifiePar): static
    {
        $this->modifiePar = $modifiePar;

        return $this;
    }

    public function getModifiePar(): ?Utilisateur
    {
        return $this->modifiePar;
    }

    public function __clone()
    {
        $this->id = null;
        $this->dateCreation = null;
        $this->dateModification = null;
        $this->creePar = null;
        $this->modifiePar = null;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
