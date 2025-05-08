<?php

namespace App\Service;

use App\Entity\Document;
use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Twig\Environment;

class SchoolMailService
{
    protected $templating;
    protected $em;
    protected $mailer;
    protected $documentManager;
    protected $router;

    protected $from;
    protected $reply;
    protected $appName;
    /** @var UtilisateurRepository */
    protected $utilisateurRepository;
    protected $kernelRootDir;
    protected $domaine;
    protected $security;

    public function __construct(
        Environment $templating,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        DocumentManager $documentManager,
        RouterInterface $router,
        Security $security,
        $companyName,
        $appName,
        $fromAddress,
        $replyAddress,
        $kernelRootDir,
        $domaine
    ) {
        $this->companyName = $companyName;
        $this->appName = $appName;
        $this->from = $fromAddress;
        $this->reply = $replyAddress;
        $this->em = $em;
        $this->mailer = $mailer;
        $this->documentManager = $documentManager;
        $this->router = $router;
        $this->security = $security;
        $this->utilisateurRepository = $this->em->getRepository(Utilisateur::class);
        $this->templating = $templating;
        $this->kernelRootDir = $kernelRootDir;
        $this->domaine = $domaine;
    }


    public function notifierEnvoi(ListeAlimentation $liste, Utilisateur $valideur)
    {
        $subject = 'SCHOOL : School ";
        $template = 'Mail/ENVOYEE/notification_min.html.twig';

        // Confirmer l'envoi aux utilisateurs
        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN'], $liste->getMinistere());


        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $valideur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }

        // Notifier les valideurs
        $subject = 'SCHOOL : School ";
        $template = 'Mail/ENVOYEE/notification_val.html.twig';
        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN_VAL'], $liste->getMinistere());
        // Envoyer une notification aux valideurs
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                'liste' => $liste,
                'utilisateur' => $utilisateur,
            ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }


    /*
     * (non-PHPdoc)
     * @see \FOS\UserBundle\Mailer\MailerInterface::sendConfirmationEmailMessage()
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = 'security/email/confirmation_registration.email.twig'; // $this->parameters['confirmation.template'];
        $domaine = $this->domaine;

        $router = $this->router;

        $url = $router->generate('initialisation_mot_de_passe', [
            'confirmationToken' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_PATH);

        $url = $domaine.$url;

        $rendered = $this->templating->render($template, [
            'utilisateur' => $user,
            'confirmationUrl' => $url,
        ]);

        $this->sendMessage($user, 'Confirmation de la création de votre compte CLEVER-SCHOOL', $rendered);
    }


    public function envoyerEmailCreationCompteUtilisateur(Utilisateur $utilisateur): \App\Entity\Email
    {
        $email = new Email(
            $utilisateur->getEmail(),
            null,
            'Confirmation de création de compte',
            'email/confirmation_registration.email.twig',
            
            [
                'confirmationToken' => $utilisateur->getConfirmationToken(),
            ]
        );

        return $this->send($email);
    }

    /*
     * (non-PHPdoc)
     * @see \FOS\UserBundle\Mailer\MailerInterface::sendResettingEmailMessage()
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = 'security/email/password_resetting.email.twig';
        $domaine = $this->domaine;

        $router = $this->router;

        $url = $router->generate('reinitialisation_mot_de_passe', [
            'confirmationToken' => $user->getConfirmationToken(),
        ], UrlGeneratorInterface::ABSOLUTE_PATH);
        $url = $domaine.$url;

        $rendered = $this->templating->render($template, [
            'utilisateur' => $user,
            'confirmationUrl' => $url,
        ]);
        $this->sendMessage($user, 'Réinitialisation de votre mot de passe SCHOOL', $rendered);
    }

    /
    /**
     * @param string $mail
     * @param string $subject
     * @param string $content
     * @return void
     */
    public function sendTestMail(string $mail, string $subject, string $content): void
    {
        $body = $this->templating->render('Mail/TEST/mail.test.html.twig', [
            'content' => $content,
        ]);

        $mailObject = new Email();
        $mailObject
            ->from(new Address($this->from, $this->appName))
            ->to($mail)
            ->subject($subject)
            ->html($body)
            ->replyTo(new Address($this->reply, $this->appName));

        $this->mailer->send($mailObject);
    }

    protected function sendMessage(Utilisateur $to, $subject, $body, $piecesJointes = null, Utilisateur $from = null)
    {
        // Ajouter le message à la messagerie clever-school
        /* @var $message Message */
        $message = new Message();


        if (!$from) {
            $from = $this->em->getRepository(Utilisateur::class)->findOneBy(['email' => 'noreply-clever-school@yahoo.fr']);
        }

        $message->setDestinataire($to)
            ->setObjetMessage($subject)
            ->setContenuMessage($body);


        if ($piecesJointes) {
            foreach ($piecesJointes as $pieceJointe) {
                $message->addPieceJointe($pieceJointe);
            }
        }

        $this->em->persist($message);
        $this->em->flush();

        // On envoie un mail si le flag recevoirNotifSCHOOL de l'utilisateur est à : 1
        if ($to->getRecevoirNotifSchool()) {
            $mail = new Email();
            $mail
                ->to($to->getEmail())
                ->subject($subject)
                ->html($body)
                ->replyTo(new Address($this->reply, $this->appName));

            if ($piecesJointes) {
                foreach ($piecesJointes as $pieceJointe) {
                    /* @var $pieceJointe Document */
                    $mail->attachFromPath($pieceJointe->getAbsolutePath(), $pieceJointe->getNom());
                }
            }
            $this->mailer->send($mail);
        }
    }
}
