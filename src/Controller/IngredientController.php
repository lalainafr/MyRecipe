<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    /** 
     * This function displays all ingredient
     *
     * @param IngredientRepository $repo
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient', name: 'app_ingredient_index', methods: ['GET'])]
    public function index(IngredientRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repo->findBy(['user' => $this->getUser()]), // query: affiche les ingredients de l'utilisateut connecté
            $request->query->getInt('page', 1), // numéro de la page
            10 // limite par page
        );
        
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }
    
    #[IsGranted('ROLE_USER')]
    #[Route('/ingredient/nouveau', name: 'app_ingredient_new', methods:['GET', 'POST'])]
    public function new( Request $request, EntityManagerInterface $em): Response
    {
        $ingredient = new Ingredient;
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $ingredient = $form->getData();
            $ingredient->setUser($this->getUser());
            $em->persist($ingredient);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre ingredient a été créé avec succès !'
            );

            return $this->redirectToRoute('app_ingredient_index');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Security("is_granted('ROLE_USER')")]
    #[Route('/ingredient/edition/{id}', name: 'app_ingredient_edit', methods:['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, IngredientRepository $repo, int $id): Response
    {
        $ingredient = $repo->findOneBy(["id" => $id]);
        $form =  $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            $this->addFlash(
                'success',
                'Votre ingredient a été modifié avec succès !'
            );
            return $this->redirectToRoute('app_ingredient_index');
        }
        
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }
    
    
    #[Security("is_granted('ROLE_USER') and user === ingredient.getUser()")]
    #[Route('/ingredient/suppression/{id}', name:'app_ingredient_delete', methods:['GET'])]
    public function delete(IngredientRepository $repo, int $id, EntityManagerInterface $em, Ingredient $ingredient) : Response
    {
        $ingredient = $repo->findOneBy(["id" => $id]);
        $em->remove($ingredient);
        $em->flush();

        $this->addFlash(
            'success',
            'Votre ingredient a été supprimé avec succès !'
        );

        return $this->redirectToRoute('app_ingredient_index');
    }

}

