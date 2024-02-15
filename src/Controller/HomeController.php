<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;



class HomeController extends AbstractController
{
#[Route("/", name: "app_home", methods: ['GET'])]
    public function index(): Response
    {

        return $this->render('home/index.html.twig', [

        ]);
    }

    #[Route('/about', name: 'app_about', methods: ['GET'])]
    public function about(Request $request): Response
    {
        return $this->render('about/index.html.twig');
    }
    #[Route(path: '/rgaa', name: 'toggle_rgaa', methods: ['GET'])]
    public function mode(Request $request): JsonResponse
    {
        if ($request->isXmlHttpRequest()) {
            /* @var $session Session */
            $session = $request->getSession();
            $status = $session->get('rgaaActif', false);
            $session->set('rgaaActif', !$status);

            return new JsonResponse(['status' => 'ok']);
        }

        return new JsonResponse(['status' => 'ko'], 404);
    }

    #[Route(path: '/_rgaa', name: 'rgaa', methods: ['GET'])]
    public function rgaa(): Response
    {
        return $this->render('rgaa.html.twig');
    }


}
