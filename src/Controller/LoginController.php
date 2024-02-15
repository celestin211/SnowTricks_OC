<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * Login function
     *
     * @Route("/login", name="account_login")
     *
     * @param AuthenticationUtils $utils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'username' => $username,
            'error' => $error ? $error->getMessage() : null,
        ]);
    }
    /**
       * Logout function
       *
       * @Route("/logout", name="account_logout")
       *
       * @return void
       */
      public function logout(): void
      {
      }
  }
