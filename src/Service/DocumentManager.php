<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\File;

class DocumentManager
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /*
     * Cette fonction retourne un document à partir d'un filePath passé en paramètre (créer un document dans la base, si on n'a pas déjà un doc
     * avec le même checksum (calculé avec md5_file).
     * Si un doc avec le même checksum existe, on le retourne (on ne crée pas un nouveau doc )
     */
    public function createDocumentFromFilePath($filePath)
    {
        $documentRepository = $this->em->getRepository(Document::class);

        //Générer le checksum du document en paramètre
        $checksum = md5_file($filePath);

        $documentFounded = $documentRepository->findOneBy(['checksum' => $checksum]);

        //Si un doc avec le même checksum existe, on le retourne
        if (null !== $documentFounded) {
            return $documentFounded;
        }

        //Sinon, on crée un nouveau document
        $document = new Document();

        $document->setChecksum($checksum);

        //Créer un fichier à partir du filePath
        $file = new File($filePath);

        $document->setFile($file);

        //Enregitrer le document dans la base
        $this->em->persist($document);
        $this->em->flush();

        return $document;
    }

    /*
     * Cette fonction permet de supprimer les documents (array) passés en paramètres
     *
     */

    public function deleteDocuments($documentsAPurger)
    {
        /* @var $documentAPurger Document */
        foreach ($documentsAPurger as $documentAPurger) {
            $this->em->remove($documentAPurger);
        }
    }

    /*
     * On vérifie si $utilisateur peut supprimer un document de $ligne
     */

    public function peutSupprimer(Utilisateur $utilisateur, Ligne $ligne)
    {
        if ($utilisateur->getMinistere() == $ligne->getAgent()->getMinistereRattachement()) {
            return true;
        }

        return false;
    }

    public function purgerBrouillons()
    {
        // récupérer les documents à purger (les documents qui trainent)
        $documentsAPurger = $this->em->getRepository(Document::class)->getDocumentsAPurger();

        //Supprimer les documents à purger
        $this->deleteDocuments($documentsAPurger);
    }


}
