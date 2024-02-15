<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Service\Paging;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route(path: '/admin')]

class AdminController extends AbstractController
{

    #[Route('/', name: "admin_dashboard", methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function dashboard(TrickRepository $repoTrick): Response
    {
        $tricks = $repoTrick->findBy([], ['id' => 'DESC'], 4);
        return $this->render('admin/dashboard.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/realisations', name: "realisations", methods: ['GET'])]
    public function realisations(Request $request): Response
    {

        return $this->render('admin/realisation.html.twig', [
        ]);
    }

}
