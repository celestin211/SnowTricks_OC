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


class RegisterController extends AbstractController
{
  /**
   * Display register form
   *
   * @Route("/register", name="account_register")
   *
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param UserPasswordEncoderInterface $encoder
   *
   * @return Response
   *
   * @throws Exception
   */
  public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
  {
      $user = new User();
      $form = $this->createForm(RegistrationType::class, $user);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          $password = $encoder->encodePassword($user, $user->getPassword());
          $createdAt = new \DateTime();
          $confirmation_token = md5(random_bytes(60));
          $user->setPassword($password)
              ->setAvatar('default-avatar.jpg')
              ->setCreatedAt($createdAt)
              ->setConfirmed(false)
              ->setRole('ROLE_USER')
              ->setConfirmationToken($confirmation_token);
          $manager->persist($user);
          $manager->flush();
          $subject = 'Confirmation de compte';
          $content = $this->renderView('emails/registration.html.twig', [
              'username' => $user->getUsername(),
              'id' => $user->getId(),
              'token' => $user->getConfirmationToken(),
              'address' => $request->server->get('SERVER_NAME')
          ]);
          $headers = 'From: "Snowtricks"<bosongo@celestinservices.fr>' . "\n";
          $headers .= 'Reply-To: bosongo@celestinservices.fr' . "\n";
          $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
          $headers .= 'Content-Transfer-Encoding: 8bit';
          mail($user->getEmail(), $subject, $content, $headers);
          $this->addFlash('success', 'Félicitation votre compte a bien été crée, un email vous a été envoyé pour le confirmer.');
          return $this->redirectToRoute('account_register');
      }
      return $this->render('account/register.html.twig', [
          'form' => $form->createView(),
      ]);
  }
}
