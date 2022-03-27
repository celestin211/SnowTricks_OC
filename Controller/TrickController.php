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

class TrickController extends AbstractController
{
    /**
     * Find user tricks
     *
     * @Route("/profile/trick/{page<\d+>?1}", name="trick_user")
     *
     * @param int $page
     * @param Paging $paging
     *
     * @return Response
     */
    public function find($page, Paging $paging): Response
    {
        $paging->setEntityClass(Trick::class)
            ->setCurrentPage($page)
            ->setLimit($this->getParameter('trick_limit'))
            ->setCriteria(['user' => $this->getUser()]);

        return $this->render('trick/user-trick.html.twig', [
            'paging' => $paging,
        ]);
    }

    /**
     * Find all tricks
     *
     * @Route("/admin/trick/{page<\d+>?1}", name="admin_trick")
     *
     * @param int $page
     * @param Paging $paging
     *
     * @return Response
     */
    public function findAll($page, Paging $paging): Response
    {
        $paging->setEntityClass(Trick::class)
            ->setCurrentPage($page)
            ->setLimit($this->getParameter('trick_limit'));

        return $this->render('trick/admin-trick.html.twig', [
            'paging' => $paging,
        ]);
    }

}
