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

class CommentFindAllController extends AbstractController
{


  /**
   * Find user comment
   *
   * @Route("/profile/comment/{page<\d+>?1}", name="comment_user")
   *
   * @param int $page
   * @param Paging $paging
   *
   * @return Response
   */
  public function find($page, Paging $paging): Response
  {
      $paging->setEntityClass(Comment::class)
          ->setCurrentPage($page)
          ->setLimit($this->getParameter('comment_limit'))
          ->setCriteria(['user' => $this->getUser()]);

      return $this->render('comment/user-comment.html.twig', [
          'paging' => $paging,
      ]);
  }

  /**
   * Find all comments
   *
   * @Route("/admin/comment/{page<\d+>?1}", name="admin_comment")
   *
   * @param int $page
   * @param Paging $paging
   *
   * @return Response
   */
  public function findAll($page, Paging $paging): Response
  {
      $paging->setEntityClass(Comment::class)
          ->setCurrentPage($page)
          ->setLimit($this->getParameter('comment_limit'));

      return $this->render('comment/admin-comment.html.twig', [
          'paging' => $paging,
      ]);
  }
}
