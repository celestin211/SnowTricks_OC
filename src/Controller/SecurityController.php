<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\Security\ChangePasswordType;
use App\Form\Security\DemandeReinitialisationPasswordType;
use App\Form\Security\ReinitialisationPasswordType;
use App\Manager\SecurityManager;
use App\Security\SecurityVoter;
use App\Util\ApiUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig',
            ["last_username" => $lastUsername,
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/login", name="api_user_login", methods={"POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @param ApiUtils $apiUtils
     * @return Response
     */
    public function login_user(AuthenticationUtils $authenticationUtils, ApiUtils $apiUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        $apiUtils->successResponse("");

        return new JsonResponse($apiUtils->getResponse(), Response::HTTP_OK);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    #[IsGranted('ROLE_USER')]
    #[Route(path: '/change-password', name: 'utilisateur_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, SecurityManager $securityManager): RedirectResponse|Response
    {
        $this->denyAccessUnlessGranted(SecurityVoter::CHANGER_MOT_DE_PASSE);
        $form = $this->createForm(ChangePasswordType::class);
        $utilisateur = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Utilisateur $utilisateur */
            $securityManager->changePassword($utilisateur, $form->get('newPassword')->getData());
            $this->addFlash(
                'success',
                'Mot de passe modifié avec succès'
            );

            return $this->redirectToRoute('utilisateur_profil');
        }

        return $this->render('security/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/demande-reinitialisation-mot-de-passe', name: 'demande_reinitialisation_mot_de_passe', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function demandeReinitialisationMotDePasse(Request $request, SecurityManager $securityManager): RedirectResponse|Response
    {
        $form = $this->createForm(DemandeReinitialisationPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $securityManager->demandeReinitialisationMotDePasse($email);

            $this->addFlash(
                'notice',
                "Un email de réinitialisation de mot de passe vient d'être envoyé à l'adresse ".$email
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/demande_reinitialisation_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/reinitialisation-mot-de-passe/{confirmationToken}', name: 'reinitialisation_mot_de_passe', methods: ['GET', 'POST'])]
    public function reinitialisationMotDePasse(Request $request, $confirmationToken, SecurityManager $securityManager, EntityManagerInterface $em): RedirectResponse|Response
    {
        return $this->initialisationReinitialisationMotDePasse($request, $confirmationToken, $securityManager, $em, 'security/reinitialisation_password.html.twig');
    }

    #[Route(path: '/initialisation-mot-de-passe/{confirmationToken}', name: 'initialisation_mot_de_passe', methods: ['GET', 'POST'])]
    public function initialisationMotDePasse(Request $request, $confirmationToken, SecurityManager $securityManager, EntityManagerInterface $em): RedirectResponse|Response
    {
        return $this->initialisationReinitialisationMotDePasse($request, $confirmationToken, $securityManager, $em, 'security/initialisation_password.html.twig');
    }

    private function initialisationReinitialisationMotDePasse(Request $request, $confirmationToken, SecurityManager $securityManager, EntityManagerInterface $em, $template): RedirectResponse|Response
    {
        $utilisateurRepository = $em->getRepository(Utilisateur::class);
        $utilisateur = $utilisateurRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        if (!$utilisateur) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ReinitialisationPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $securityManager->reinitialiserMotDePasse($utilisateur, $form->get('newPassword')->getData(), $request);

            return $this->redirectToRoute('accueil');
        }

        return $this->render($template, [
            'form' => $form->createView(),
        ]);
    }
}
