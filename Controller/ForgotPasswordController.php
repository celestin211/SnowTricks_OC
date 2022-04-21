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

class ForgotPasswordController extends AbstractController
{



      /**
       * Forgot password
       *
       * @Route("/forgot-password", name="account_forgot")
       *
       * @param Request $request
       * @param UserRepository $repo
       * @param EntityManagerInterface $manager
       *
       * @return Response
       *
       * @throws Exception
       */
      public function forgotPassword(Request $request, UserRepository $repo, EntityManagerInterface $manager): Response
      {
          $passwordForgot = new PasswordForgot();
          $form = $this->createForm(PasswordForgotType::class, $passwordForgot);
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              if ($repo->findOneBy(['username' => $passwordForgot->getUsername()])) {
                  /** @var User $user */
                  $user = $repo->findOneBy(['username' => $passwordForgot->getUsername()]);
                  $confirmation_token = md5(random_bytes(60));//Hachage du jéton
                  $user->setConfirmationToken($confirmation_token);
                  $manager->flush();
                  $subject = 'Réinitialisation du mot de passe';
                  $content = $this->renderView('emails/forgot-password.html.twig', [
                      'username' => $user->getUsername(),
                      'id' => $user->getId(),
                      'token' => $user->getConfirmationToken(),
                      'address' => $request->server->get('SERVER_NAME'),
                  ]
                  );
                  $headers = 'From: "Snowtricks"<bosongo@celestinservices.fr>' . "\n";
                  $headers .= 'Reply-To: bosongo@celestinservices.fr' . "\n";
                  $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
                  $headers .= 'Content-Transfer-Encoding: 8bit';
                  mail($user->getEmail(), $subject, $content, $headers);
                  $this->addFlash('success', 'Veuillez vérifier votre boite email, un email vient de vous être envoyé pour réinitialiser votre mot de passe !');
                  return $this->redirectToRoute('account_forgot');
              }

              $this->addFlash('danger', 'Cet utilisateur n\'existe pas.');
              return $this->redirectToRoute('account_forgot');
          }
          return $this->render('account/forgot-password.html.twig', [
              'form' => $form->createView(),
          ]);
      }
}
