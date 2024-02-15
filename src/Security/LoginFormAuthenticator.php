<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    private $urlGenerator;

    private $userProvider;

    private $checkCredentials;

    public function __construct(UrlGeneratorInterface $urlGenerator, UserProviderInterface $userProvider, bool $checkCredentials)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userProvider = $userProvider;
        $this->checkCredentials = $checkCredentials;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $userBadge = new UserBadge($credentials['username'], function ($userIdentifier) {
            return $this->userProvider->loadUserByIdentifier($userIdentifier);
        });

        if(!$this->checkCredentials) {
            return new SelfValidatingPassport($userBadge);
        }

        $passport = new Passport($userBadge, new PasswordCredentials($credentials['password']));
        $passport->addBadge(new CsrfTokenBadge('authenticate', $credentials['csrf_token']));
        $passport->addBadge(new RememberMeBadge());
        $passport->addBadge(new PasswordUpgradeBadge($credentials['password'], $this->userProvider));

        return $passport;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $request->getSession()->get('_security.main.target_path')) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('bac_homepage'));
    }

    private function getCredentials(Request $request): array
    {
        $credentials = [];
        $credentials['csrf_token'] = $request->request->get('_csrf_token');
        $credentials['username'] = trim($request->request->get('email', ''));
        $credentials['password'] = $request->request->get('password', '');

        if (\strlen($credentials['username']) > Security::MAX_USERNAME_LENGTH) {
            throw new BadCredentialsException('Email non valide');
        }

        $request->getSession()->set(Security::LAST_USERNAME, $credentials['username']);

        return $credentials;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        if ($exception instanceof BadCredentialsException) {
            $exception = new BadCredentialsException('Email ou mot de passe incorrect');
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
