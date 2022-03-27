<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentUpdateType;
use App\Service\Paging;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentUpdateController extends AbstractController
{

  /**
   * Update comment for user
   *
   * @Route("/profile/comment/edit/{id}", name="comment_update")
   * @Security(
   *      "user === comment.getUser()",
   *      message = "Ce commentaire ne vous appartient pas !"
   * )
   *
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param Comment $comment
   *
   * @return Response
   */
  public function update(Request $request, EntityManagerInterface $manager, Comment $comment): Response
  {
      $form = $this->createForm(CommentUpdateType::class, $comment);//création de commentaires
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {//si le bouton valider est enclaché alors le commentaire est modifié
          $comment->setCreatedAt(new \DateTime());//récupère la date postée par l'itulisateur
          $manager->flush();
          $this->addFlash('success', 'Votre commentaire a été mis à jour.');
          return $this->redirectToRoute('comment_user');
      }
      return $this->render('comment/user-comment-update.html.twig', [
          'comment' => $comment,
          'form' => $form->createView(),
      ]);
  }
  /**
   * Update comment for admin
   *
   * @Route("/admin/comment/edit/{id}", name="comment_admin_update")
   *
   * @param Request $request
   * @param EntityManagerInterface $manager
   * @param Comment $comment
   *
   * @return Response
   */
  public function updateAll(Request $request, EntityManagerInterface $manager, Comment $comment): Response
  {
      $form = $this->createForm(CommentUpdateType::class, $comment);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
          $manager->flush();
          $this->addFlash('success', 'Le commentaire a été mis à jour.');
          return $this->redirectToRoute('admin_comment');
      }
      return $this->render('comment/admin-comment-update.html.twig', [
          'comment' => $comment,
          'form' => $form->createView(),
      ]);
  }

}
