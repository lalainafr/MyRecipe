<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $contact = new Contact();
        if($this->getUser()){
            $contact->setFullname($this->getUser()->getFullName())
                ->setEmail($this->getUser()->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $contact = $form->getData();
            $em->persist($contact);
            $em->flush();

            // // EMAIL
            $email = (new TemplatedEmail())
            ->from($contact->getEmail())
            ->to(new Address('r.lalainafr1@gmail.com'))
            ->subject('Thanks for signing up!')
        
            // path of the Twig template to render
            ->htmlTemplate('emails/contact.html.twig')                
        
            // pass variables (name => value) to the template
            ->context([
                'contact' => $contact,
            ]);

            $mailer->send($email);

            // $email = (new Email())
            // ->from('hello@example.com')
            // ->to('you@example.com')
            // //->cc('cc@example.com')
            // //->bcc('bcc@example.com')
            // //->replyTo('fabien@example.com')
            // //->priority(Email::PRIORITY_HIGH)
            // ->subject('Time for Symfony Mailer!')
            // ->text('Sending emails is fun again!')
            // ->html('<p>See Twig integration for better HTML integration!</p>');


            $this->addFlash(
                'success',
                'Votre demande a été soumise avec succès !'
            );
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
}
