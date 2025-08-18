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

class AccountController extends AbstractController
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
        return $this->render('account/login.html.twig', [
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
    {}

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
            $headers = 'From: "Snowtricks"<webdev@jeandescorps.fr>' . "\n";
            $headers .= 'Reply-To: jean.webdev@gmail.com' . "\n";
            $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
            $headers .= 'Content-Transfer-Encoding: 8bit';
            mail($user->getEmail(), $subject, $content, $headers);
            $this->addFlash('success', 'Votre compte a bien été crée, un email vous a été envoyé pour le confirmer.');
            return $this->redirectToRoute('account_register');
        }
        return $this->render('account/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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
        if (!$request->query->get('token')) {
            throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour vous valider !');
        }

        $id = $request->query->get('id');
        $token = $request->query->get('token');

        /** @var User $user */
        $user = $repo->findOneBy(['id' => $id]);
        if ($user->getId() && $user->getConfirmationToken() === $token) {
            $user->setConfirmationToken(null)
                ->setConfirmed(true);
            $manager->flush();
            $this->addFlash('success', 'Votre compte est validé ! Connectez-vous !');
            return $this->redirectToRoute('account_login');
        }

        throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour vous valider !');
    }
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
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $oldAvatar = $user->getAvatar();

        // Crée le formulaire
        $form = $this->createForm(AccountType::class, $user, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $currentAvatar */
            $currentAvatar = $form->get('avatar')->getData();

            if ($currentAvatar instanceof UploadedFile) {
                // Générer un nom unique et déplacer le fichier
                $avatarName = $this->generateUniqueFileName() . '.' . $currentAvatar->guessExtension();
                $currentAvatar->move(
                    $this->getParameter('images_directory'),
                    $avatarName
                );
                $user->setAvatar($avatarName);

                // Supprimer l'ancien avatar sauf si c'est l'image par défaut
                if ($oldAvatar && $oldAvatar !== 'default-avatar.jpg') {
                    $oldFile = $this->getParameter('images_directory') . '/' . $oldAvatar;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            } else {
                // Aucun changement d'avatar, garder l'ancien
                $user->setAvatar($oldAvatar);
            }

            $manager->flush();
            $this->addFlash('success', 'Votre compte a été mis à jour.');

            return $this->redirectToRoute('account_profile');
        }

        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

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
            $this->addFlash('success', 'Votre mot de passe a été mis à jour.');
            return $this->redirectToRoute('account_password');
        }
        return $this->render('account/update-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

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
                $confirmation_token = md5(random_bytes(60));
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
                $headers = 'From: "Snowtricks"<webdev@jeandescorps.fr>' . "\n";
                $headers .= 'Reply-To: jean.webdev@gmail.com' . "\n";
                $headers .= 'Content-Type: text/html; charset="iso-8859-1"' . "\n";
                $headers .= 'Content-Transfer-Encoding: 8bit';
                mail($user->getEmail(), $subject, $content, $headers);
                $this->addFlash('success', 'Un email vient de vous être envoyé pour réinitialiser votre mot de passe !');
                return $this->redirectToRoute('account_forgot');
            }

            $this->addFlash('danger', 'Cet utilisateur n\'existe pas.');
            return $this->redirectToRoute('account_forgot');
        }
        return $this->render('account/forgot-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Reset password (forgot)
     *
     * @Route("/reset-password", name="account_reset")
     *
     * @param Request $request
     * @param UserRepository $repo
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     *
     * @throws Exception
     */
    public function resetPassword(Request $request, UserRepository $repo, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        if (!$request->query->get('id')) {
            throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour réinitialiser votre mot de passe !');
        }
        if (!$request->query->get('token')) {
            throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour réinitialiser votre mot de passe !');
        }

        $token = $request->query->get('token');
        $id = $request->query->get('id');

        $passwordReset = new PasswordReset();
        /** @var User $user */
        $user = $repo->findOneBy(['id' => $id]);

        $form = $this->createForm(PasswordResetType::class, $passwordReset);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($user->getId()) {
                if ($user->getEmail() === $passwordReset->getEmail()) {
                    if ($user->getConfirmationToken() === $token) {
                        $newPassword = $passwordReset->getNewPassword();
                        $password = $encoder->encodePassword($user, $newPassword);
                        $user->setConfirmationToken(null)
                            ->setPassword($password);
                        $manager->persist($user);
                        $manager->flush();
                        $this->addFlash('success', 'Votre mot de passe a été mis à jour ! Connectez-vous !');
                        return $this->redirectToRoute('account_login');
                    }

                    throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour réinitialiser votre mot de passe !');
                }

                $this->addFlash('success', 'Cette adresse email n\'est pas celle associée à votre compte !');
                return $this->redirectToRoute('account_login');
            }

            throw new Exception('Veuillez cliquer sur le lien fournit dans l\'email qui vous a été envoyé pour réinitialiser votre mot de passe !');
        }

        return $this->render('account/reset-password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Generate unique file name
     *
     * @return string
     */
    private function generateUniqueFileName(): string
    {
        return md5(uniqid('', true));
    }
}
