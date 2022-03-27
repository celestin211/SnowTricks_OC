<?php

namespace App\Controller;

use App\Entity\PasswordForgot;
use App\Entity\PasswordReset;
use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\PasswordForgotType;
use App\Form\PasswordResetType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class GeneratedUniqueFileNameController extends AbstractController
{


  /**
   * Generate unique file name
   *
   * @return string
   */
  private function generateUniqueFileName(): string
  {
      return md5(uniqid('', true));
  }
}
