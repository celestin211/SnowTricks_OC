<?php

namespace App\Security;

use App\Entity\Utilisateur;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }

        if (!$user->isEnabled()) {
            throw new CustomUserMessageAccountStatusException('Votre compte utilisateur est désactivé.');
        }

        if ($user->isLocked()) {
            throw new CustomUserMessageAccountStatusException("Compte bloqué : veuillez utiliser la fonctionnalité 'Mot de passé oublié'");
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof Utilisateur) {
            return;
        }
    }
}
