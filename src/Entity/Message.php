<?php

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\MessageRepository;
use App\Traits\GenericTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'message')]
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message implements GenericTraitInterface
{
    use GenericTrait;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'destinataire', nullable: false)]
    private ?Utilisateur $destinataire;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(name: 'expediteur', nullable: false)]
    private ?Utilisateur $expediteur;

    #[ORM\Column(name: 'objet', type: 'string', length: 255)]
    private ?string $objetMessage;

    #[ORM\Column(name: 'contenu', type: 'text')]
    private ?string $contenuMessage;

    #[ORM\ManyToMany(targetEntity: Document::class, cascade: ['persist'])]
    private ?Collection $piecesJointes; // Dans une relation ManyToMany, on ajoute l'attribut d'association dans l'entité propriétaire

    #[ORM\Column(name: 'favoris', type: 'boolean')]
    private ?bool $favoris;

    #[ORM\Column(name: 'lu', type: 'boolean')]
    private ?bool $lu;

    #[ORM\Column(name: 'date_lecture', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateLecture;

    #[ORM\Column(name: 'date_envoi', type: 'datetime')]
    private ?\DateTimeInterface $dateEnvoi;

    #[ORM\Column(name: 'supprime', type: 'boolean')]
    private ?bool $supprime;

    public function __construct()
    {
        // Initialiser un message
        $this->piecesJointes = new ArrayCollection();
        $this->dateEnvoi = new \DateTime();
        $this->favoris = 0;
        $this->supprime = 0;
        $this->lu = 0;
    }

    public function setDestinataire(?Utilisateur $destinataire): static
    {
        $this->destinataire = $destinataire;

        return $this;
    }

    public function getDestinataire(): ?Utilisateur
    {
        return $this->destinataire;
    }

    public function setExpediteur(?Utilisateur $expediteur): static
    {
        $this->expediteur = $expediteur;

        return $this;
    }

    public function getExpediteur(): ?Utilisateur
    {
        return $this->expediteur;
    }

    public function setObjetMessage(?string $objetMessage): static
    {
        $this->objetMessage = $objetMessage;

        return $this;
    }

    public function getObjetMessage(): ?string
    {
        return $this->objetMessage;
    }

    public function setContenuMessage(?string $contenuMessage): static
    {
        $this->contenuMessage = $contenuMessage;

        return $this;
    }

    public function getContenuMessage(): ?string
    {
        return $this->contenuMessage;
    }

    public function setFavoris(?bool $favoris): static
    {
        $this->favoris = $favoris;

        return $this;
    }

    public function getFavoris(): ?bool
    {
        return $this->favoris;
    }

    public function setLu(?bool $lu): static
    {
        $this->lu = $lu;

        return $this;
    }

    public function getLu(): ?bool
    {
        return $this->lu;
    }

    public function setDateLecture(?\DateTimeInterface $dateLecture): static
    {
        $this->dateLecture = $dateLecture;

        return $this;
    }

    public function getDateLecture(): ?\DateTimeInterface
    {
        return $this->dateLecture;
    }

    public function setDateEnvoi(?\DateTimeInterface $dateEnvoi): static
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    public function getDateEnvoi(): ?\DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    public function setSupprime(?bool $supprime): static
    {
        $this->supprime = $supprime;

        return $this;
    }

    public function getSupprime(): ?bool
    {
        return $this->supprime;
    }

    public function getPiecesJointes(): ?Collection
    {
        return $this->piecesJointes;
    }

    public function setPiecesJointes(?Collection $piecesJointes): static
    {
        $this->piecesJointes = $piecesJointes;

        return $this;
    }

    public function addPieceJointe(?Document $piecesJointe): static
    {
        $this->piecesJointes[] = $piecesJointe;

        return $this;
    }

    public function getObjetTronquee(): ?string
    {
        $longueurMax = 42;

        if (strlen($this->objetMessage) <= $longueurMax) {
            return $this->objetMessage;
        }

        return mb_substr($this->objetMessage, 0, $longueurMax, 'UTF-8') . '...';
    }

    public function isFavoris(): ?bool
    {
        return $this->favoris;
    }

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function isSupprime(): ?bool
    {
        return $this->supprime;
    }

    public function addPiecesJointe(Document $piecesJointe): static
    {
        if (!$this->piecesJointes->contains($piecesJointe)) {
            $this->piecesJointes->add($piecesJointe);
        }

        return $this;
    }

    public function removePiecesJointe(Document $piecesJointe): static
    {
        $this->piecesJointes->removeElement($piecesJointe);

        return $this;
    }
}
