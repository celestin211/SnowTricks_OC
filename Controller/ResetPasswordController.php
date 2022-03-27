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

class   ResetPasswordController extends AbstractController
{



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
}
