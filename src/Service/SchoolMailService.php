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

    // /////////////////////////********* Notifications campagnes *********///////////////////////////

    // $utilisateur: celui qui fait l'action
    public function notifierTerminerCampagne(Campagne $campagne, Utilisateur $utilisateur)
    {
        // Notifier tous les utilisateur sauf l'acteur
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong().'" : Terminée';
        $template = 'Mail/Campagne/TERMINEE/notification_min.html.twig';

        $this->notifierActifsSaufAdminEtActeur($campagne, $utilisateur, $subject, $template);
    }

    // $utilisateur: celui qui fait l'action : peut être == null si la commande est appelée via un crontab
    public function notifierCloturerCampagne(Campagne $campagne)
    {
        // Notifier tous les utilisateur sauf l'acteur
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong().'" : Clôturée';
        $template = 'Mail/Campagne/CLOTUREE/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_DGAFP', 'ROLE_MIN', 'ROLE_MIN_VAL']);

        // Envoyer une notification aux utilisateurs actifs qui ne sont pas Admin ni acteur
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                        'campagne' => $campagne,
                        'utilisateur' => $utilisateur,
                    ]);

            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    public function notifierOuvrirCampagne(Campagne $campagne)
    {
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong().'" : Ouverte';

        // notifier les utilisateurs DGAFP
        /** @var array $utilisateursActifs */
        $utilisateurs = $this->utilisateurRepository->findUtilisateursDgafp();
        foreach ($utilisateurs as $utilisateur) {
            if (EnumTypeCampagneType::POPULATION_GLOBALE == $campagne->getTypeCampagne()) {
                $template = 'Mail/Campagne/OUVERTE/AM/notification_dgafp.html.twig';
            } else {
                $template = 'Mail/Campagne/OUVERTE/notification_min.html.twig';
            }

            $body = $this->templating->render($template, [
                    'campagne' => $campagne,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }

        // notifier les utilisateurs gestionnaires
        $utilisateurs = $this->utilisateurRepository->findUtilisateursMinistere();
        foreach ($utilisateurs as $utilisateur) {
            if (EnumTypeCampagneType::POPULATION_GLOBALE == $campagne->getTypeCampagne()) {
                $template = 'Mail/Campagne/OUVERTE/AM/notification_min.html.twig';
                $maquetteXLSX = $this->kernelRootDir.'/public/Maquettes/population-globale/PopulationGlobale.xlsx';
                // Créer des documents à partir des pièces jointes (s'ils n'ont pas été déjà créés)
                $piecesJointesDocument = [$this->documentManager->createDocumentFromFilePath($maquetteXLSX)];
            } else {
                $template = 'Mail/Campagne/OUVERTE/notification_min.html.twig';
                $piecesJointesDocument = [];
            }

            $body = $this->templating->render($template, [
                'campagne' => $campagne,
                'utilisateur' => $utilisateur,
            ]);
            $this->sendMessage($utilisateur, $subject, $body, $piecesJointesDocument);
        }
    }

    // $utilisateur: celui qui fait l'action
    public function notifierModifierCampagne(Campagne $campagne, Utilisateur $acteur)
    {
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong().'" : Modification';
        $template = 'Mail/Campagne/MODIFIEE/notification_min.html.twig';

        $this->notifierActifsSaufAdminEtActeur($campagne, $acteur, $subject, $template);
    }

    public function notifierSupprimerCampagne(Campagne $campagne, Utilisateur $utilisateur = null)
    {
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong().'" : Suppression';
        $template = 'Mail/Campagne/SUPPRIMEE/suppression.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_DGAFP', 'ROLE_MIN', 'ROLE_MIN_VAL']);

        // Envoyer une notification aux utilisateurs actifs qui ne sont pas Admin ni acteur
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                        'campagne' => $campagne,
                        'utilisateur' => $utilisateur,
                    ]);

            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // /////////////////////////********* Notifications rappels *********///////////////////////////

    // Envoyer un rappel aux ministères qui n'ont pas soumis une liste avant le délai de rappel
    public function envoyerRappel(Campagne $campagne, Ministere $ministere = null, Utilisateur $utilisateur = null)
    {
        $subject = 'SIGNAC : '.$campagne->getTypeCampagneImpressionLong()." : Envoyez votre liste d'alimentation";
        $template = 'Mail/rappels/rappelMinisteres.html.twig';

        // Si $ministere==NULL: il s'agit d'un rappel destiné à un utilisateur
        // Si $utilisateur==NULL: il s'agit d'un rappel destiné à un ministère
        if (null == $ministere) {
            $body = $this->templating->render($template, [
                'campagne' => $campagne,
                'utilisateur' => $utilisateur,
            ]);

            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // ////////////////////////////////////////////////////////////////////////
    public function notifierSupprimerListe($campagneId, Utilisateur $auteurListe, Utilisateur $currentUser)
    {
        $campagne = $this->em->getRepository(Campagne::class)->find($campagneId);

        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpressionLong()."\" : Suppression de votre liste d'agents";
        $template = 'Mail/SUPPRIMEE/notification.html.twig';

        // Envoyer une notif à l'auteur de la liste si ce n'est pas lui qui fait l'action, s'il est activé et n'est pas bloqué
        if ($auteurListe->getId() != $currentUser->getId() && $auteurListe->isEnabled() && !$auteurListe->isLocked()) {
            $body = $this->templating->render($template, [
                'campagne' => $campagne,
                'utilisateur' => $auteurListe,
                'currentUser' => $currentUser,
            ]);
            $this->sendMessage($auteurListe, $subject, $body);
        }
    }

    public function notifierEnvoi(ListeAlimentation $liste, Utilisateur $valideur)
    {
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Accusé de réception';
        $template = 'Mail/ENVOYEE/notification_min.html.twig';

        // Confirmer l'envoi aux utilisateurs
        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni valideurs ni DGAFP ni Admin
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
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression()."\" : Réception d'une liste d'agents";
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

    public function notifierValidationMinistere(ListeAlimentation $liste)
    {
        // Notification des gestionnaires et des autres valideurs
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : validation ministère';
        $template = 'Mail/VALIDEE_MINISTERE/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        foreach ($utilisateurs as $utilisateur) {
            /* @var $utilisateur Utilisateur */
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }

        // Notification de la DGAFP
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Liste en attente de validation';
        $template = 'Mail/VALIDEE_MINISTERE/notification_dgafp.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateursDgafp();

        // Envoyer une notification aux utilisateurs actifs qui ont un rôle DGAFP et qui ne sont pas Admin
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Notification: Le valideur du ministère rejete une liste d'alimentation
    public function notifierRejetMin(ListeAlimentation $liste, Utilisateur $acteur)
    {
        // Notification de tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Rejet du valideur';

        $template = 'Mail/REJETEE_MINISTERE/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->getId() != $acteur->getId()) {
                $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $acteur,
                ]);
                $this->sendMessage($utilisateur, $subject, $body);
            }
        }
    }

    // Envoyer une notification à l'utilisateur et au valideur
    public function notifierValidationDGAFP(ListeAlimentation $liste)
    {
        // Notification de tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Validation DGAFP';
        $template = 'Mail/VALIDEE_DGAFP/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Envoyer une notification à l'utilisateur et au valideur
    public function notifierRejetDGAFP(ListeAlimentation $liste)
    {
        // Notification de tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Rejet DGAFP';
        $template = 'Mail/REJETEE_DGAFP/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    public function notifierEnvoiPromouvables(ListeAlimentation $liste)
    {
        // Notifier les gestionnaires
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Accusé de réception de la liste des promouvables';
        $template = 'Mail/ENVOYEE_PROMOUVABLES/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni valideur ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }

        // Notifier les valideurs
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Réception de la liste des promouvables';
        $template = 'Mail/ENVOYEE_PROMOUVABLES/notification_val.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux valideur actifs qui sont ni DGAFP ni Admin
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Envoyer une notification à la DGAFP et à l'utilisateur
    public function notifierValidationMinPromouvables(ListeAlimentation $liste, Utilisateur $valideur)
    {
        // Notifier tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Validation ministère de la liste des promouvables';
        $template = 'Mail/VALIDEE_PROMOUVABLES_MINISTERE/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->getId() != $valideur->getId()) {
                $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $valideur,
                ]);
                $this->sendMessage($utilisateur, $subject, $body);
            }
        }

        // Notifier la DGAFP
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression()."\" : Réception d'une liste des promouvables";
        $template = 'Mail/VALIDEE_PROMOUVABLES_MINISTERE/notification_dgafp.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateursDgafp();

        // Envoyer une notification aux utilisateurs actifs qui sont DGAFP mais pas Admin
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Notification: Le valideur du ministère rejete une liste de promouvables
    public function notifierRejetPromouvablesMin(ListeAlimentation $liste, Utilisateur $acteur)
    {
        // Notification de tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Rejet de la liste des promouvables';

        $template = 'Mail/REJETEE_PROMOUVABLES_MINISTERE/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->getId() != $acteur->getId()) {
                $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $acteur,
                ]);
                $this->sendMessage($utilisateur, $subject, $body);
            }
        }
    }

    // Envoyer une notification à la DGAFP et à l'utilisateur
    public function notifierValidationPromouvablesDGAFP(ListeAlimentation $liste, Utilisateur $acteur)
    {
        // Notifier tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Validation DGAFP de la liste des promouvables';

        $template = 'Mail/VALIDEE_PROMOUVABLES_DGAFP/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $acteur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Envoyer une notification à l'utilisateur et au valideur
    public function notifierRejetPromouvablesDGAFP(ListeAlimentation $liste, Utilisateur $acteur)
    {
        // Notifier Tous les utilisateurs du ministère
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Rejet DGAFP de la liste des promouvables';
        $template = 'Mail/REJETEE_PROMOUVABLES_DGAFP/notification_min.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_MIN', 'ROLE_MIN_VAL'], $liste->getMinistere());

        // Envoyer une notification aux utilisateurs actifs qui sont ni DGAFP ni Admin
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'liste' => $liste,
                    'utilisateur' => $utilisateur,
                    'valideur' => $acteur,
                ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
    }

    // Envoyer une notification aux utilisateurs ayant le rôle DGAFP
    public function notifierExpirerCampagne(Campagne $campagne)
    {
        $subject = 'SIGNAC : Campagne "'.$campagne->getTypeCampagneImpression().'" : Expirée';
        $template = 'Mail/Campagne/EXPIREE/notification_DGAFP.html.twig';

        $utilisateurs = $this->utilisateurRepository->findUtilisateursDgafp();

        // Envoyer une notification aux utilisateurs actifs qui ne sont pas Admin ni DGAFP_VIP
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            $body = $this->templating->render($template, [
                    'utilisateur' => $utilisateur,
                    'campagne' => $campagne,
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
        $this->sendMessage($user, 'Réinitialisation de votre mot de passe SIGNAC', $rendered);
    }

    // Fonction d'envoi de mail de notification aux utilisateurs actifs ayant le rôle DGAFP
    public function notifierDevalidationListePromouvables(Professeur $liste)
    {
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Dévalidation de la liste des promouvables du Ministère '.$liste->getMinistere()->getLibelleCourt();

        $template = 'Mail/DEVALIDATION/devalidation_liste_promouvables.html.twig';

        $this->notifierDevalidation($liste, $subject, $template);
    }

    // Fonction d'envoi de mail de notification aux utilisateurs actifs ayant le rôle DGAFP
    public function notifierDevalidationFichesProposition(Professeur $liste)
    {
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression().'" : Dévalidation des fiches de proposition du Ministère '.$liste->getMinistere()->getLibelleCourt();

        $template = 'Mail/DEVALIDATION/devalidation_fiches_proposition.html.twig';

        $this->notifierDevalidation($liste, $subject, $template);
    }

    // Fonction d'envoi de mail de notification aux utilisateurs actifs ayant le rôle DGAFP
    public function notifierDevalidationFichesIntegration(Professeur $liste)
    {
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression()."\" : Dévalidation des fiches d'intégration du Ministère ".$liste->getMinistere()->getLibelleCourt();

        $template = 'Mail/DEVALIDATION/devalidation_fiches_integration.html.twig';

        $this->notifierDevalidation($liste, $subject, $template);
    }

    // Fonction d'envoi de mail de notification aux utilisateurs actifs ayant le rôle DGAFP
    public function notifierDevalidationListeAgents(Professeur $liste)
    {
        $subject = 'SIGNAC : Campagne "'.$liste->getCampagne()->getTypeCampagneImpression()."\" : Dévalidation de la liste d'agents du Ministère ".$liste->getMinistere()->getLibelleCourt();

        $template = 'Mail/DEVALIDATION/devalidation_liste_agents.html.twig';

        $this->notifierDevalidation($liste, $subject, $template);
    }

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

    private function notifierDevalidation(Professeur $liste, $subject, $template)
    {
        $utilisateursDgafp = $this->utilisateurRepository->findUtilisateursDgafp();

        // Envoyer une notification aux utilisateurs actifs qui ont un rôle Prof
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateursDgafp as $utilisateur) {
            $body = $this->templating->render($template, [
                'utilisateur' => $utilisateur,
                'liste' => $liste,
                'utilisateurCourant' => $this->security->getUser(),
            ]);
            $this->sendMessage($utilisateur, $subject, $body);
        }
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

        // On envoie un mail si le flag recevoirNotifSignac de l'utilisateur est à : 1
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

    private function notifierActifsSaufAdminEtActeur(Campagne $campagne, Utilisateur $acteur, string $subject, string $template)
    {
        $utilisateurs = $this->utilisateurRepository->findUtilisateurs(['ROLE_DGAFP', 'ROLE_MIN', 'ROLE_MIN_VAL']);

        // Envoyer une notification aux utilisateurs actifs qui ne sont pas Admin ni acteur
        /** @var Utilisateur $utilisateur */
        foreach ($utilisateurs as $utilisateur) {
            if ($utilisateur->getId() != $acteur->getId()) {
                $body = $this->templating->render($template, [
                    'campagne' => $campagne,
                    'utilisateur' => $utilisateur,
                ]);

                $this->sendMessage($utilisateur, $subject, $body);
            }
        }
    }
}
