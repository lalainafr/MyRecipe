<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;

use App\Service\MailService;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailService $mailService): Response
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

            // On utilise le service MAILSENDER pour envoyer le mail
            $mailService->sendEmail
            (
                $contact->getEmail(),
                $contact->getSubject(),
                'emails/contact.html.twig',
                ['contact' => $contact],
            );
            
            // EMAIL
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
