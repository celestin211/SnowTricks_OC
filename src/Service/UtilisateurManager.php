<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UtilisateurManager
{
    private $excelWriter;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $roleService;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var SecurityManager
     */
    private $securityManager;
    /**
     * @var UserPasswordHasherInterface
     */
    private $passwordHasher;
    /**
     * @var SchoolMailService
     */
    private $mailer;

    public function __construct(
        AuthorizationCheckerInterface $roleService,
        EntityManagerInterface $em,
        SecurityManager $securityManager,
        UserPasswordHasherInterface $passwordHasher,
        SchoolMailService $mailer
    ) {
        $this->roleService = $roleService;
        $this->em = $em;

        $this->securityManager = $securityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
    }

    /*
     * Cette fonction évalue si l'utilisateur connecté ($utilisateurConnecte) peut effectuer une action (new/edit ...)
     *  sur l'utilisateur selon son rôle ($roleUtilisateurAction) passé en paramètre
     */
    public function peutFaireActionAdmin(Utilisateur $utilisateurConnecte, $roleUtilisateurAction)
    {
        if (!in_array($roleUtilisateurAction, ['ROLE_ADMIN']) && 'ROLE_USER' == $utilisateurConnecte->getRoles()[0]) {
            throw new AccessDeniedException("Accès non autorisé. L'administrateur est notifié de l'action");
        }
    }

    /*
     * Cette fonction évalue si l'utilisateur connecté ($utilisateurConnecte) peut voir
     *  l'utilisateur ($utilisateurAction) et selon son rôle passé en paramètre
     */
    public function peutVoir(Utilisateur $utilisateurConnecte, Utilisateur $utilisateurAction)
    {
        if ($utilisateurAction->getId() == $utilisateurConnecte->getId()) {
            return true;
        }

        if ($this->roleService->isGranted('ROLE_ADMIN', $utilisateurConnecte)) {
            return true;
        }

        // Un utilisateur dgafp ne peut pas voir un utilisateur admin ou dgafp
        if (
            $this->roleService->isGranted('ROLE_ADMIN', $utilisateurConnecte)
            && !$utilisateurAction->hasRole('ROLE_USER')
        ) {
            return true;
        }

        throw new AccessDeniedException("Accès non autorisé. L'administrateur est notifié de l'action");
    }

    public function extractUsers($utilisateurs)
    {
        return $this->excelWriter->extractUsers($utilisateurs);
    }

    public function creerUtilisateur(Utilisateur $utilisateur, $role)
    {
        // mot de passe généré par defaut
        $motDePasse = $this->securityManager->generateToken();
        $motDePasseEncode = $this->passwordHasher->hashPassword($utilisateur, $motDePasse);

        $utilisateur->setPassword($motDePasseEncode);
        $utilisateur->setConfirmationToken($this->securityManager->generateToken());
        $utilisateur->setRoles([$role]);
        $this->em->persist($utilisateur);
        $this->mailer->sendConfirmationEmailMessage($utilisateur);
    }

    public function editeUtilisateur(Utilisateur $utilisateur, $role)
    {
        // mot de passe généré par defaut
        $motDePasse = $this->securityManager->generateToken();
        $motDePasseEncode = $this->passwordHasher->hashPassword($utilisateur, $motDePasse);

        $utilisateur->setPassword($motDePasseEncode);
        $utilisateur->setConfirmationToken($this->securityManager->generateToken());
        $utilisateur->setRoles([$role]);
        $this->em->persist($utilisateur);
        $this->mailer->sendConfirmationEmailMessage($utilisateur);
    }


    public function redifinirPasswordUtilisateur( $data)
    {
        $data->setEnabled(true);
        $data->setLocked(false);
        $data->setConfirmationToken(null);
        $data->setForceChangePassword(true);
        $data->setNbConnexionKO(0);
        $data->setPassword($this->passwordHasher->hashPassword( $data['newPassword']));
        $this->em->flush();
    }


}
