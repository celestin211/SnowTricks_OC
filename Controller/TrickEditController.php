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

class TrickEditController extends AbstractController
{


      /**
       * Edit trick
       *
       * @Route("/trick/{id}/edit", name="trick_edit")
       * @Security(
       *      "user === trick.getUser() || is_granted('ROLE_ADMIN')",
       *      message = "Ce trick ne vous appartient pas !"
       * )
       *
       * @param Request $request
       * @param EntityManagerInterface $manager
       * @param Trick $trick
       * @param ImageRepository $repo_image
       *
       * @return Response
       */
      public function edit(Request $request, EntityManagerInterface $manager, Trick $trick, ImageRepository $repo_image): Response
      {
          $path = $this->getParameter('images_directory');
          $images = $repo_image->findBy(array('trick' => $trick->getId()));
          $form = $this->createForm(TrickType::class, $trick);
          foreach ($trick->getImages() as $image) {
              $image->setPath($path);
          }
          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {

              $trick->setPath($path);

              for ($i = 0; $i < 8; $i++) {
                  foreach ($trick->getImages() as $image) {
                      $image->setPath($path);
                  }
              }

              for ($i = 0; $i < 8; $i++) {
                  foreach ($trick->getVideos() as $video) {
                      if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $video->getUrl(), $match)) {
                          $video_id = $match[1];
                          $video->setUrl('https://www.youtube.com/embed/' . $video_id);
                      }
                  }
              }

              $trick->setUpdatedAt(new \Datetime);
              $manager->flush();
              $this->addFlash('success', 'Bravo votre trick a été modifié avec succés !');
              return $this->redirectToRoute('trick_show', ['id' => $trick->getId()]);
          }

          return $this->render('trick/edit-trick.html.twig', [
              'form' => $form->createView(),
              'trick' => $trick,
              'images' => $images,
          ]);

      }

    }
