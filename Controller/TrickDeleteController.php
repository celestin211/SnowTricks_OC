<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\ImageRepository;
use App\Service\Paging;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickDeleteController extends AbstractController
{


      /**
       * Delete trick for user
       *
       * @Route("/profile/trick/delete/{id}", name="trick_delete")
       * @Security(
       *      "user === trick.getUser()",
       *      message = "Ce trick ne vous appartient pas !"
       * )
       *
       * @param EntityManagerInterface $manager
       * @param Trick $trick
       *
       * @return Response
       */
      public function delete(EntityManagerInterface $manager, Trick $trick): Response
      {
          $manager->remove($trick);
          $manager->flush();
          $this->addFlash('success', 'Le trick a été supprimé.');
          return $this->redirectToRoute('trick_user');
      }

      /**
       * Delete trick for admin
       *
       * @Route("/admin/trick/delete/{id}", name="trick_admin_delete")
       *
       * @param EntityManagerInterface $manager
       * @param Trick $trick
       *
       * @return Response
       */
      public function deleteAll(EntityManagerInterface $manager, Trick $trick): Response
      {
          $manager->remove($trick);
          $manager->flush();
          $this->addFlash('success', 'Bravo le trick a été supprimé.');
          return $this->redirectToRoute('admin_trick');
      }
    }
