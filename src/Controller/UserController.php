<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/utilisateur/edition/{id}', name: 'app_user_edit')]
    public function editProfile(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher): Response
    {
        // Si l'utisateur n'est pas connecté, le renvoyer vers la page de connexion
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }
        
        // Si l'utisateur connecté, le renvoyer vers la page de connexion
        if($this->getUser() !== $user){
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            // Verifier si le mot de passe tapé correspond à plainPassword en bdd
            if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){
                $user = $form->getData();
                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées !'
                );
                
                return $this->redirectToRoute('app_recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect!'
                );
            }
        }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'app_user_edit_password')]
    public function editUserPasssword(User $user, Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {      
            // Si l'utisateur connecté, le renvoyer vers la page de connexion
            if($this->getUser() !== $user){
                return $this->redirectToRoute('app_login');
            }

        // Creer le formilaire qui n'est pas mappé avec une entité (user)
        $form = $this->createForm(UserPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            // Verifier si le mot de passe tapé correspond à plainPassword en bdd
            // dd($user, $form->getData()['plainPassword'], $form->getData()['newPassword']);
            if($hasher->isPasswordValid($user, $form->getData()['plainPassword'])){
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->getData()['newPassword']
                        )
                    );        
                $user->setPassword(
                    $hasher->hashPassword(
                        $user,
                        $form->getData()['newPassword']
                        )
                    );        

                $em->persist($user);
                $em->flush();

                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées !'
                );
                
                return $this->redirectToRoute('app_recipe_index');
            } else {
                $this->addFlash(
                    'warning',
                    'Le mot de passe renseigné est incorrect!'
                );
            }
        }
        return $this->render('pages/user/edit_password.html.twig', [
            'form' => $form->createView()
        ]);
        }
}
