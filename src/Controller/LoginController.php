<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class LoginController extends AbstractController
{
    #[Route('/connexion', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('pages/login/index.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError()
        ]);
    }

    #[Route('/deconnexion', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
    }

    #[Route('/inscription', name: 'app_registration')]
    public function registration(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface  $hasher): Response
    {
        $user = new User;
        $user->setRoles(['ROLE_USER']);
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
     
        if ($form->isSubmitted() && $form->isValid()){
            $plain_pwd = $form->getData()->getPlainPassword();
            $hash = $hasher->hashPassword($user, $plain_pwd);
            $form->getData()->setpassword($hash);
           
            $em->persist($form->getData());
            $em->flush();

            $this->addFlash(
                'success',
                'Votre compte a été créé avec succès !'
            );

            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('pages/registration/index.html.twig', [
           'form' => $form->createView()
        ]);
    }
}