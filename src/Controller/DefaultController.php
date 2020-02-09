<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Mailer;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(\Swift_Mailer $mailer)
    {   
        return $this->render('default/index.html.twig');
    }
}
