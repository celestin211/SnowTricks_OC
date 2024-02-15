<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\DocumentAccueilRepository;
use App\Traits\GenericTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity(repositoryClass: DocumentAccueilRepository::class)]
class DocumentAccueil implements GenericTraitInterface
{
    use GenericTrait;

    #[Assert\Valid]
    #[ORM\OneToOne(targetEntity: Document::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Document $document;

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): void
    {
        $this->document = $document;
    }
}
