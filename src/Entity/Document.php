<?php

declare(strict_types=1);

namespace App\Entity;

use App\Interfaces\GenericTraitInterface;
use App\Repository\DocumentRepository;
use App\Traits\GenericTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Table(name: 'document')]
#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discr', type: 'string')]
#[ORM\HasLifecycleCallbacks]
class Document implements GenericTraitInterface
{
    use GenericTrait;

    #[ORM\Column(type: 'string', length: 512)]
    protected ?string $nom;

    #[ORM\Column(type: 'string', length: 1024)]
    protected ?string $path = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $brouillon = false;

    // propriété utilisé temporairement pour la suppression
    protected ?string $filenameForRemove;

    #[Assert\File(maxSize: '5M', mimeTypes: ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'application/pdf', 'application/msword', 'application/vnd.oasis.opendocument.text', 'application/vnd.oasis.opendocument.spreadsheet', 'application/vnd.ms-powerpoint', 'application/vnd.oasis.opendocument.presentation', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/octet-stream'], maxSizeMessage: 'La taille du fichier ne doit pas dépasser 5 Mo', mimeTypesMessage: "Ce type de fichier n'est pas autorisé")]
    protected ?File $file = null;

    protected ?bool $toDelete = null;

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setFile(?File $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function getAbsolutePath(): ?string
    {
        return null === $this->path ? null : $this->getRootDir().'/'.$this->path;
    }

    public function getWebPath(): ?string
    {
        return null === $this->path ? null : self::getDir().'/'.$this->path;
    }

    public static function getRootDir(): string
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../public/'.self::getDir();
    }

    protected static function getDir(): string
    {
        // on se débarrasse de « __DIR__ » afin de ne pas avoir de problème lorsqu'on affiche
        // le document/image dans la vue.
        return 'documents';
    }

    #[ORM\PostPersist]
    #[ORM\PostUpdate]
    public function save(): void
    {
        if (null === $this->file) {
            return;
        }

        // s'il y a une erreur lors du déplacement du fichier, une exception
        // va automatiquement être lancée par la méthode rename(). Cela va empêcher
        // proprement l'entité d'être persistée dans la base de données si
        // erreur il y a

        $docrootDir = realpath(__DIR__.'/../../../public/');

        $realpathDocument = realpath($this->file->getPathname());
        $docrootDirLength = strlen($docrootDir);

        // déplace du fichier s'il n'est pas dans web
        if (substr($realpathDocument, 0, $docrootDirLength) !== $docrootDir) {
            rename($this->file->getPathname(), $this->getRootDir().'/'.$this->path);
            unset($this->file);
        } else {
            // S'il est dans web, on le copie sans le déplacer
            copy($this->file->getPathname(), $this->getRootDir().'/'.$this->path);
        }
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function prePersist()
    {
        if (null !== $this->file) {
            // génére un nom unique
            $this->path = sha1(uniqid((string) mt_rand(), true)).'.'.$this->file->getExtension();

            $this->dateModification = new \DateTime();
        } else {
            return;
        }

        if (null === $this->path || null === $this->nom) {
            throw new \Exception('Erreur lors du chargement du fichier');
        }
    }

    #[ORM\PreRemove]
    public function storeFilenameForRemove(): void
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    #[ORM\PostRemove]
    public function removeUpload(): void
    {
        if ($this->filenameForRemove && file_exists($this->filenameForRemove)) {
            unlink($this->filenameForRemove);
        }
    }

    public function __construct($fileName = null)
    {
        $this->dateCreation = new \DateTime();
        $this->dateModification = new \DateTime();

        if ($fileName) {
            $this->nom = $fileName;
            $filePath = $this->getRootDir().'/'.$fileName;

            $this->setFile(new File($filePath));
        }
    }

    public function setBrouillon(bool $brouillon): static
    {
        $this->brouillon = $brouillon;

        return $this;
    }

    public function getBrouillon(): bool
    {
        return $this->brouillon;
    }

    #[Assert\Callback(groups: ['Default'])]
    public function validate(ExecutionContextInterface $context): void
    {
        $extensionsAcceptees = ['csv', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'odt', 'ods', 'ppt', 'pptx', 'odp', 'jpg', 'jpeg', 'png'];

        if ($this->file) {
            $fileName = $this->file->getClientOriginalName();

            $arrayTmp = explode('.', $fileName);
            $fileExtension = strtolower(end($arrayTmp));

            if (!in_array($fileExtension, $extensionsAcceptees)) {
                $context->buildViolation('Extension non valide')
                    ->atPath('file')
                    ->addViolation();
            }
        }

        if (null === $this->id && null === $this->file && null === $this->path) {
            $context->buildViolation('Fichier obligatoire')
                ->atPath('file')
                ->addViolation();
        }
    }

    public function isToDelete(): ?bool
    {
        return $this->toDelete;
    }


    public function setToDelete(?bool $toDelete): static
    {
        $this->toDelete = $toDelete;

        return $this;
    }

    public function isBrouillon(): ?bool
    {
        return $this->brouillon;
    }
}
