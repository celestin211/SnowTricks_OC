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

class TrickAddController extends AbstractController
{

  /**
   * Add trick
   *
   * @Route("/profile/add-trick", name="trick_add")
   *
   * @param Request $request
   * @param EntityManagerInterface $manager
   *
   * @return Response
   */
  public function add(Request $request, EntityManagerInterface $manager): Response
  {
      $trick = new Trick();
      $path = $this->getParameter('images_directory');
      $form = $this->createForm(TrickType::class, $trick);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          if ($form->get('file')->getData() === null) {
              $image = 'default-trick.jpg';
              $trick->setImage($image);
          }

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

          $createdAt = new \DateTime();

          $trick->setCreatedAt($createdAt)
              ->setUser($this->getUser());

          $manager->persist($trick);
          $manager->flush();
          $this->addFlash('success', 'Châpeau l\'artiste Votre trick a été créée avec succés !');
          return $this->redirectToRoute('trick_user');
      }

      return $this->render('trick/add-trick.html.twig', [
          'form' => $form->createView(),
      ]);
  }
}
