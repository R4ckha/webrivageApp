<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\Bridge\Google\Transport\GmailSmtpTransport;
use Symfony\Component\Mailer\Mailer;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(\Swift_Mailer $mailer)
    {   
        
            $name = "Admin";
            /*$message = (new \Swift_Message('Hello Email'))
            ->setFrom('gratien.therond@gmail.com')
            ->setTo('gratien.therond@gmail.com')
            ->setBody(
                $this->renderView(
                    // templates/emails/updateDiscount.html.twig
                    'emails/updateDiscount.html.twig',
                    ['name' => $name]
                ),
                'text/html'
            );

        $mailer->send($message);
        */
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }
}
