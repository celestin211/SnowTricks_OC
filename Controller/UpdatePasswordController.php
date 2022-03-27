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

class UpdatePasswordController extends AbstractController
{



      /**
       * Update password
       *
       * @Route("profile/update-password", name="account_password")
       *
       * @param Request $request
       * @param UserPasswordEncoderInterface $encoder
       * @param EntityManagerInterface $manager
       *
       * @return Response
       */
      public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager): Response
      {
          $passwordUpdate = new PasswordUpdate();
          /** @var User $user */
          $user = $this->getUser();
          $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              $newPassword = $passwordUpdate->getNewPassword();
              $password = $encoder->encodePassword($user, $newPassword);
              $user->setPassword($password);
              $manager->persist($user);
              $manager->flush();
              $this->addFlash('success', 'Bravo votre mot de passe a été mis à jour.');
              return $this->redirectToRoute('account_password');
          }
          return $this->render('account/update-password.html.twig', [
              'form' => $form->createView(),
          ]);
      }
}
