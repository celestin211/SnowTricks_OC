<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Util\Menu;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * La calsse MenuListener dispose d'une methode qui s'execute sur un evenement kernel.request pour rafraichir le menu stocke en session.
 */
class MenuListener
{
    private $securityContext;
    private $securityAuthorizationChecker;
    private $libelleActif = 'Accueil';
    private $router;

    public function __construct(TokenStorageInterface $securityContext, AuthorizationCheckerInterface $securityAuthorizationChecker, RouterInterface $router)
    {
        $this->securityContext = $securityContext;
        $this->securityAuthorizationChecker = $securityAuthorizationChecker;
        $this->router = $router;
    }

    /**
     * Methode qui s'execute des qu'une page est appelee
     * Elle rafraichit le menu stocke en session.
     */
    public function refreshMenu(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (null !== $this->securityContext->getToken()) {
            // Recuperation de l'objet Utilisateur
            $user = $this->securityContext->getToken()->getUser();

            if (null !== $user && $user instanceof Utilisateur) {
                // recuperation des roles de l'utilisateur
                //$roles = $user->getRoles();

                // Recuperation du menu en fonction l'utilisateur
                $menu = Menu::getMenu($user, $this->securityAuthorizationChecker, $this->router);

                // Activation de l'item correspondant a la route appelee
                $menu = Menu::setActiveMenu($event->getRequest()->attributes->get('_route'), $menu);

                //$menu = Menu::setActiveMenuByLibelle($this->libelleActif, $menu);

                // Enregistrement du menu dans la session
                $event->getRequest()->getSession()->set('menu', $menu);

                // Construction du fil d'Ariane en fonction du menu
                $breadCrumb = Menu::getPathMenu($menu);

                // Enregistrement du fil d'Ariane dans la session
                $event->getRequest()->getSession()->set('breadCrumb', $breadCrumb);
            }
        }
    }
}
