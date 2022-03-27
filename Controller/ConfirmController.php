<?php

namespace App\Controller;

use App\Entity\PasswordForgot;
use App\Entity\PasswordReset;
use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordForgotType;
use App\Form\PasswordResetType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class ConfirmController extends AbstractController
{


  /**
   * Email confirmation
   *
   * @Route("/confirm", name="account_confirm")
   *
   * @param Request $request
   * @param UserRepository $repo
   * @param EntityManagerInterface $manager
   *
   * @return Response
   *
   * @throws Exception
   */
  public function confirm(Request $request, UserRepository $repo, EntityManagerInterface $manager): ?Response
  {
      if (!$request->query->get('id')) {
          throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour vous valider !');
      }
      if (!$request->query->get('token')) {//récupère le jéton d'activation de compte
          throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour vous valider !');
      }

      $id = $request->query->get('id');
      $token = $request->query->get('token');

      /** @var User $user */
      $user = $repo->findOneBy(['id' => $id]);//récupère juste identié de l'utilisateur
      if ($user->getId() && $user->getConfirmationToken() === $token) {
          $user->setConfirmationToken(null)
              ->setConfirmed(true);
          $manager->flush();
          $this->addFlash('success', 'Votre compte est validé ! Connectez-vous !');
          return $this->redirectToRoute('account_login');
      }

      throw new Exception('SVP cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour vous valider !');
  }
}
