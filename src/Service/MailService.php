<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{

    /**
     *
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
        string $from, 
        string $subject,
        string $htmlTemplate,
        array $context,
        string $to = 'r.lalainafr1@gmail.com', 
        ): void
    {
        $email = (new TemplatedEmail())
        ->from($from)
        ->to($to)
        ->subject($subject)
    
        // path of the Twig template to render
        ->htmlTemplate($htmlTemplate)                
        // pass variables (name => value) to the template
        ->context($context);

        $this->mailer->send($email);        
    }
}
