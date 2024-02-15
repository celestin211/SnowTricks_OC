<?php

namespace App\Controller;

use App\Entity\Connexion;
use App\Entity\Utilisateur;
use App\Form\Security\ReinitialisationPasswordType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Security\UtilisateurVoter;
use App\Service\UtilisateurManager;
use App\Util\Util;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Knp\Component\Pager\PaginatorInterface;


/**
 * Utilisateur controller.
 */
#[Route(path: '/utilisateur')]
class UtilisateurController extends AbstractController
{

    /**
     * Lists all Utilisateur entities.
     *
     */
    #[Route(path: '/', name: 'utilisateur', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index()
    {
        return $this->render('Utilisateur/index.html.twig');
    }

    /**
     * Finds and displays a Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/show', name: 'utilisateur_show', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function show( UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        //Voter
        $utilisateur = $this->getUser();
        /*@var $dernieresconnexion Connexion */
        $dernieresConnexions = $em->getRepository(Connexion::class)->derniereConnexionUtilisateurs($utilisateur);

        return $this->render('Utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
            'dernieresConnexions' => $dernieresConnexions,
        ]);
    }

    /**
     * Displays a form to create a new Utilisateur entity.
     *
     */
    #[Route(path: '/new_register', name: 'utilisateur_new_register_admin', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function register(Request $request, UtilisateurManager $utilisateurManager)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            //Todo: Créer un voter
            $utilisateurManager->peutFaireActionAdmin($this->getUser(), $role);
            $utilisateurManager->creerUtilisateur($utilisateur, $role);
            $this->addFlash('success', 'Utilisateur "'.$utilisateur->getEmail().'" créé avec succès !');

            return $this->redirectToRoute('utilisateur');
        }

        return $this->render('utilisateur/new-users.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }
    /**
     * Displays a form to create a new Utilisateur entity.
     *
     */
    #[Route(path: '/new', name: 'utilisateur_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, UtilisateurManager $utilisateurManager)
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            //Todo: Créer un voter
            $utilisateurManager->peutFaireActionAdmin($this->getUser(), $role);
            $utilisateurManager->creerUtilisateur($utilisateur, $role);
            $this->addFlash('success', 'Utilisateur "'.$utilisateur->getEmail().'" créé avec succès !');

            return $this->redirectToRoute('utilisateur');
        }

        return $this->render('utilisateur/new-users.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }



    /**
     * Displays a form to edit an existing Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/edit', name: 'utilisateur_edit',  methods: ['GET', 'POST', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit( Request $request, $id, EntityManagerInterface $em, UtilisateurManager $utilisateurManager)
    {
        $this->denyAccessUnlessGranted(UtilisateurVoter::MODIFIER_COMPTE_UTILISATEUR);
        $utilisateur = $em->getRepository(Utilisateur::class)->findOneBy(['id'=>$id]);
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get('role')->getData();
            $utilisateurManager->peutFaireActionAdmin($this->getUser(), $role);
            $utilisateur->setRoles([$role]);
            $em->flush();
            $this->addFlash('success', 'Utilisateur "'.$utilisateur->getEmail().'" mis à jour avec succès !');

            return $this->redirectToRoute('utilisateur');
        }

        return $this->render('Utilisateur/new-user.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/delete', name: 'utilisateur_delete',  methods: ['POST', 'DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Request $request, Utilisateur $utilisateur, UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        //Voter
        $roleUtilisateurASupprimer = $utilisateur->getRoles()[0];
        $utilisateurManager->peutFaireActionAdmin($this->getUser(), $roleUtilisateurASupprimer);

        $form = $this->createDeleteForm($utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($utilisateur->getId() != $this->getUser()->getId()) {
                $utilisateur->setEnabled(false);
                $em->flush();
                try {
                    $em->remove($utilisateur);
                    $em->flush();
                    $this->addFlash('success', 'Utilisateur "' . $utilisateur->getNom() . ' ' . $utilisateur->getPrenom() . '" supprimé !');
                } catch (\Exception $e) {
                    $this->addFlash('danger', "L'utilisateur " . $utilisateur->getNom() . ' ' . $utilisateur->getPrenom() . " ne peut pas être supprimé car il dispose d'un historique. Il a cependant été désactivé.");
                }
            }
        }

        return $this->redirectToRoute('utilisateur');
    }

    #[Route(path: '/profil', name: 'utilisateur_profil')]
    #[IsGranted('ROLE_USER')]
    public function profil(EntityManagerInterface $em, Request $request): Response
    {
        /* @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        //@todo: creation d'un manager
        $dernieresConnexions = $em->getRepository(Connexion::class)->derniereConnexionUtilisateurs($utilisateur);


        return $this->render('Utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
            'dernieresConnexions' => $dernieresConnexions,
        ]);
    }

    /**
     * Activer un utilisateur et redéfinir son mot de passe
     * !!!! à utiliser uniquement quand un utilisateur n'a pas reçu ou il a perdu le mail de confirmation de création de compte.
     *
     */
    #[Route(path: '/{id}/activerEtRedefinirPassword', name: 'utilisateur_activer_redefinir_password')]
    #[IsGranted('ROLE_ADMIN')]
    public function activerEtRedefinirPassword(Request $request, Utilisateur $utilisateur, UtilisateurManager $utilisateurManager)
    {
        $this->denyAccessUnlessGranted(UtilisateurVoter::ACTIVER_ET_REDEFINIR_MOT_DE_PASSE);
        $form = $this->createForm(ReinitialisationPasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $utilisateurManager->redifinirPasswordUtilisateur($utilisateur, $data);
            $this->addFlash('success', "L'utilisateur '".$utilisateur->getEmail()."' a été activé avec succès");

            return $this->redirectToRoute('utilisateur');
        }

        return $this->render('Utilisateur/activer_et_redefinir_password.html.twig', [
            'form' => $form->createView(),
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * Enable a Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/enable', name: 'utilisateur_enable', methods: ['POST', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function enable(Request $request, Utilisateur $utilisateur, UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        //Voter
        $roleUtilisateurAActiver = $utilisateur->getRoles()[0];
        $utilisateurManager->peutFaireActionAdmin($this->getUser(), $roleUtilisateurAActiver);

        $form = $this->createEnableForm($utilisateur->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setEnabled(true);
            if ($utilisateur->isLocked()) {
                $utilisateur->setLocked(false);
                $utilisateur->setNbConnexionKO(0);
            }
            $em->flush();
            $this->addFlash('success', 'Utilisateur "'.$utilisateur->getNom().' '.$utilisateur->getPrenom().'" activé !');
        }

        return $this->redirectToRoute('utilisateur');
    }

    /**
     * Deletes a Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/disable', name: 'utilisateur_disable', methods: ['POST', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function disable(Request $request, Utilisateur $utilisateur, UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        //Voter
        $roleUtilisateurADesactiver = $utilisateur->getRoles()[0];
        $utilisateurManager->peutFaireActionAdmin($this->getUser(), $roleUtilisateurADesactiver);

        $form = $this->createDisableForm($utilisateur->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($utilisateur->getId() != $this->getUser()->getId()) {
                $utilisateur->setEnabled(false);
                $em->flush();
                $this->addFlash('success', 'Utilisateur "'.$utilisateur->getNom().' '.$utilisateur->getPrenom().'" désactivé !');
            } else {
                $this->addFlash('danger', 'Vous ne pouvez pas désactiver votre propore compte utilisateur !');
            }
        }

        return $this->redirectToRoute('utilisateur');
    }

    /**
     * Unlock a Utilisateur entity.
     *
     */
    #[Route(path: '/{id}/unlock', name: 'utilisateur_unlock', methods: ['POST', 'PUT'])]
    #[IsGranted('ROLE_ADMIN')]
    public function unlock(Request $request, Utilisateur $utilisateur, UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        //Voter
        $roleUtilisateurADebloquer = $utilisateur->getRoles()[0];
        $utilisateurManager->peutFaireActionAdmin($this->getUser(), $roleUtilisateurADebloquer);

        $form = $this->createUnlockForm($utilisateur->getId());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur->setLocked(false);
            $utilisateur->setNbConnexionKO(0);
            $em->flush();
            $this->addFlash('success', 'Utilisateur "'.$utilisateur->getNom().' '.$utilisateur->getPrenom().'" debloqué !');
        }

        return $this->redirectToRoute('utilisateur');
    }

    private function createDeleteForm(Utilisateur $utilisateur)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_delete', ['id' => $utilisateur->getId()]))
            ->setMethod(Request::METHOD_DELETE)
            ->getForm();
    }

    private function createEnableForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_enable', ['id' => $id]))
            ->setMethod(Request::METHOD_PUT)
            ->getForm();
    }

    private function createDisableForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_disable', ['id' => $id]))
            ->setMethod(Request::METHOD_PUT)
            ->getForm();
    }

    /**
     * Creates a form to unlock a Utilisateur entity by id.
     *
     * @param $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createUnlockForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('utilisateur_unlock', ['id' => $id]))
            ->setMethod(Request::METHOD_PUT)
            ->getForm();
    }

    /**
     * Cette action est appelée quand un utilisateur souhaite recevoir ou pas les notifications de signac (mettre à jour le champ $recevoirNotifSignac de la table utilisateur).
     *
     */
    #[Route(path: '/update/recevoir_notification', name: 'utilisateur_update_statut_recevoir_notification', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function updateStatutRecevoirNotificationSchool(AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $em)
    {
        $idUser = $_POST['idUtilisateur'];

        //:Todo l'Ajax déclenche une erreur 500 si on désactive pas le switch en cas d'administrateur
        if ($authorizationChecker->isGranted('ROLE_ADMIN') && $this->getUser()->getId() != $idUser) {
            throw new AccessDeniedException('Accès refusé.');
        }

        /** @var $user Utilisateur */
        $user = $em->getRepository(Utilisateur::class)->find($idUser);

        if (!$user) {
            throw $this->createNotFoundException("UtilisateurController.updateStatutRecevoirNotificationCLEVERAction: aucun utilisateur n'est trouvé avec l'id: ".$user);
        }

        //Récupérer le statut actuel : $recevoirNotifSignac
        $flagActuel = $user->getRecevoirNotifSchool();

        $newFlag = !$flagActuel;

        $user->setRecevoirNotifSchool($newFlag);

        $em->flush();

        return $this->json('ok');
    }

    /**
     * extraire les utilisateurs sous forme d'un fichier excel.
     *
     */
    #[Route(path: '/télécharger', name: 'utilisateurs_extract', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function extractUsers(UtilisateurManager $utilisateurManager, EntityManagerInterface $em)
    {
        $utilisateurs = $em->getRepository(Utilisateur::class)->getUsersSaufAdmin();

        return $utilisateurManager->extractUsers($utilisateurs);
    }

    #[Route(path: '/pagination', name: 'pagination', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function pagination(Request $request, UtilisateurRepository $utilisateurRepository): \Symfony\Component\HttpFoundation\JsonResponse
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 10);
        $search = $request->get('search', ['value' => ''])['value'];
        $order = $request->get('order', [['column' => 1, 'dir' => 'asc']]);

        $nbUtilisateurs = $utilisateurRepository->searchCount($this->isGranted('ROLE_ADMIN'));

        $lignes = $utilisateurRepository->search($search, $start, $length, $order, $this->isGranted('ROLE_ADMIN'));

        $recordsFiltered = $utilisateurRepository->searchCount($this->isGranted('ROLE_ADMIN'), $search);



        $data = [];

        /* @var Utilisateur $utilisateur */
        foreach ($lignes as $ligne) {

            $utilisateur = $ligne[0];
            $statut = $ligne['statut'];

            $data[] = [
                Util::twig_title($utilisateur->getCivilite()),
                Util::twig_upper($utilisateur->getNom()),
                Util::twig_title($utilisateur->getPrenom()),
                Util::twig_lower($utilisateur->getEmail()),
                $ligne['role'],
                $this->render('Utilisateur/_include/statut.html.twig', ['utilisateur' => $utilisateur, 'statut' => $statut])->getContent(),
                $this->render('Utilisateur/_include/actions.html.twig', ['utilisateur' => $utilisateur,
                    'deleteForm' => $this->createDeleteForm($utilisateur)->createView(),
                    'enableForm' => $this->createEnableForm($utilisateur)->createView(),
                    'disableForm' => $this->createDisableForm($utilisateur)->createView(),
                    'unlockForm' => $this->createUnlockForm($utilisateur)->createView(),
                ])->getContent(),
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $nbUtilisateurs,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ];

        return $this->json($response);
    }
}

