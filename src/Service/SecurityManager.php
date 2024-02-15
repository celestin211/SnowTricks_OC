<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Utilisateur;
use App\Security\ChangePasswordAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class SecurityManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var UserPasswordHasherInterface */
    private $passwordHasher;

    /** @var SchoolMailService */
    private $mailer;

    /** @var string */
    private $dureeVieToken;

    /**
     * @var UserAuthenticatorInterface
     */
    private $authenticator;

    /**
     * @var Security
     */
    private $security;

    /**
     * @var ChangePasswordAuthenticator
     */
    private $changePasswordAuthenticator;

    public function __construct(
        EntityManagerInterface      $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SchoolMailService           $mailer,
        Security                    $security,
        ChangePasswordAuthenticator $changePasswordAuthenticator,
       // UserAuthenticatorInterface  $authenticator
    ) {
        $this->em = $entityManager;
        $this->passwordHasher = $passwordHasher;
        $this->mailer = $mailer;
       // $this->authenticator = $authenticator;
        $this->security = $security;
        $this->changePasswordAuthenticator = $changePasswordAuthenticator;
    }

    public function changePassword(Utilisateur $utilisateur, string $nouveauMotdePass)
    {
        $utilisateur->setForceChangePassword(false);
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $nouveauMotdePass);
        $utilisateur->setPassword($hashedPassword);
        $this->em->flush();
    }

    public function demandeReinitialisationMotDePasse(string $email)
    {
        /* @var Utilisateur $utilisateur */
        $utilisateur = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => $email, 'enabled' => true]);

        if (!$utilisateur) {
            return;
        }

        //@todo : si l'utilisateur est désactivé, il ne ne faut pas lui envoyer de mail de réinitialisation

        // vérifier la présence d'un token récent (moins de 24 h)
        if ($utilisateur->getConfirmationToken()
            && $utilisateur->getPasswordRequestedAt()
        ) {
            $this->mailer->sendResettingEmailMessage($utilisateur);

            return;
        }

        // Permet de conserver le même token qui est toujours valide
        if (!$utilisateur->getConfirmationToken()) {
            $confirmationToken = $this->generateToken();
            $utilisateur->setConfirmationToken($confirmationToken);
            $utilisateur->setPasswordRequestedAt(new \DateTime());
        }

        // envoyer email avec le token
        $this->mailer->sendResettingEmailMessage($utilisateur);

        $this->em->flush();
    }

    public function reinitialiserMotDePasse(Utilisateur $utilisateur, string $newPassword, Request $request)
    {
        $hashedPassword = $this->passwordHasher->hashPassword($utilisateur, $newPassword);
        $utilisateur->setPassword($hashedPassword)
            ->setConfirmationToken(null)
            ->setPasswordRequestedAt(null);

        $utilisateur->setLocked(false)
            ->setNbConnexionKO(0);
        $utilisateur->setEnabled(true);

        $this->em->flush();

        return $this->authenticator->authenticateUser(
            $utilisateur,
            $this->changePasswordAuthenticator,
            $request);
    }

    public function generateToken(): string
    {
//        return sha1(random_bytes(32));
        return hash('sha256', random_bytes(32));
    }
}
