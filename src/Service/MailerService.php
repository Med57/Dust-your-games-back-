<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;

class MailerService
{

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    
    }


    public function sendEmailWelcome($user):Response
    {
        $email = (new TemplatedEmail())
            ->from('info@api.dustyourgames.com')
            ->to($user->getEmail())
            ->subject('Bienvenue chez Dust your Games')
            ->htmlTemplate('email/new.html.twig')
            ->context(['username' => $user->getPseudoName() ]);
        $this->mailer->send($email);
        return new Response("Email Envoyé avec Succes", Response::HTTP_OK);
    }

    public function sendEmailPasswordLost($user, $pass):Response
    {

        $email = (new TemplatedEmail())
            ->from('info@api.dustyourgames.com')
            ->to($user->getEmail())
            ->subject('Mot de pass oublié')
            ->htmlTemplate('email/passwordlost.html.twig')

            ->context(['username' => $user->getPseudoName(),
                       'password' => $pass
            ]);
        $this->mailer->send($email);
        return new Response("Email Envoyé avec Succes", Response::HTTP_OK);

    }    
}
