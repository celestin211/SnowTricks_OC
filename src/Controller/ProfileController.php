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

class ProfileController extends AbstractController
{

  /**
   * Display profile and update profile form
   *
   * @Route("/profile", name="account_profile")
   *
   * @param Request $request
   * @param EntityManagerInterface $manager
   *
   * @return Response
   */
  public function profile(Request $request, EntityManagerInterface $manager)
  {
      /** @var User $user */
      $user = $this->getUser();
      $userDb = $manager->createQuery('SELECT u FROM App\Entity\User u WHERE u.id = :id')->setParameter('id', $user->getId())->getScalarResult();
      $oldAvatar = $user->getAvatar();
      $user->setAvatar(new File($this->getParameter('images_directory') . '/' . $user->getAvatar()));
      $form = $this->createForm(AccountType::class, $user, array('user' => $this->getUser()));

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
          $currentAvatar = $form->get('avatar')->getData();
          if ($currentAvatar !== null && $currentAvatar !== $user->getAvatar()) {
              $newAvatar = $form->get('avatar')->getData();
              $avatarName = $this->generateUniqueFileName() . '.' . $newAvatar->guessExtension();
              $newAvatar->move(
                  $this->getParameter('images_directory'),
                  $avatarName
              );
              $user->setAvatar($avatarName);
              if ($oldAvatar !== 'default-avatar.jpg') {
                  unlink($this->getParameter('images_directory') . '/' . $oldAvatar);
              }
          } else {
              $user->setAvatar($oldAvatar);
          }

          $manager->flush();
          $this->addFlash('success', 'Votre compte a été mis à jour.');
          return $this->redirectToRoute('account_profile');
      }
      return $this->render('account/profile.html.twig', [
          'form' => $form->createView(),
          'user' => $userDb[0],
      ]);
  }
}
