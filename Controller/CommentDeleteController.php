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

class CommentDeleteController extends AbstractController
{
/**
 * Delete comment for user
 *
 * @Route("/profile/comment/delete/{id}", name="comment_delete")
 * @Security(
 *      "user === comment.getUser()",
 *      message = "Ce commentaire ne vous appartient pas !"
 * )
 *
 * @param EntityManagerInterface $manager
 * @param Comment $comment
 *
 * @return Response
 */
public function delete(EntityManagerInterface $manager, Comment $comment): Response
{
    $manager->remove($comment);
    $manager->flush();
    $this->addFlash('success', 'Le commentaire a été supprimé.');
    return $this->redirectToRoute('comment_user');
    /*delete action for user */
}


/**
 * Delete comment for admin
 *
 * @Route("/admin/comment/delete/{id}", name="comment_admin_delete")
 *
 * @param EntityManagerInterface $manager
 * @param Comment $comment
 *
 * @return Response
 */

public function deleteAll(EntityManagerInterface $manager, Comment $comment): Response
{
    $manager->remove($comment);
    $manager->flush();
    $this->addFlash('success', 'Le commentaire a été supprimé.');
    return $this->redirectToRoute('admin_comment');
    /*admin action delete comment */
  }
}
