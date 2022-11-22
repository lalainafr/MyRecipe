<?php

namespace App\Controller;

use App\Entity\Recipe;
use DateTimeImmutable;
use App\Form\RecipeType;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecipeController extends AbstractController
{
    
    
    #[IsGranted('ROLE_USER')]
    #[Route('/recette/nouveau', name: 'app_recipe_new', methods:['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
       $recipe = new Recipe();
       $form = $this->createForm(RecipeType::class, $recipe);
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
            $recipe = $form->getData();
            // Assigner la recette créee avec l'utilisateur connecté
            $recipe->setUser($this->getUser());
            $em->persist($recipe);
            $em->flush();

            $this->addFlash(
                'success',
                'Votre recette a été créée avec succès !'
            );

            return $this->redirectToRoute('app_recipe_index');
       }
       return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
       ]);
    }

    #[Route('/recette/public', name: 'app_recipe_indexPublic', methods:['GET'])]
    public function indexPublic(RecipeRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repo->findPublicRecipe(null), // query
            $request->query->getInt('page', 1), // numéro de la page
            10 // limite par page
        );
        return $this->render('pages/recipe/indexPublic.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    // Acces autorisé aux utilisateur connectés, recette publique ou l'utilisateur a qui appartient la recette
    #[Security("is_granted('ROLE_USER') and recipe.isIsPublic() === true || user === recipe.getUser()")]
    #[Route('/recette/{id}', name: 'app_recipe_show', methods:['GET'])]
    public function show(Recipe $recipe): Response
    {
       return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
       ]);    
    }

 

    #[IsGranted('ROLE_USER')]
    #[Route('/recette', name: 'app_recipe_index', methods:['GET'])]
    public function index(RecipeRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $recipes = $paginator->paginate(
            $repo->findBy(['user' => $this->getUser()]), // query
            $request->query->getInt('page', 1), // numéro de la page
            10 // limite par page
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes
        ]);
    }
    
   

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
    #[Route('/recette/edition/{id}', name: 'app_recipe_edit', methods:['GET', 'POST'])]
    public function edit(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {

       $form = $this->createForm(RecipeType::class, $recipe);
       $form->handleRequest($request);
       if($form->isSubmitted() && $form->isValid()){
            $recipe->setUpdateAt(new DateTimeImmutable);
            $recipe = $form->getData();
            $em->flush();


            $this->addFlash(
                'success',
                'Votre recette a été modifiée avec succès !'
            );

            return $this->redirectToRoute('app_recipe_index');
       }
       return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
       ]);
    }



    #[Route('/recette/suppression/{id}', name: 'app_recipe_delete', methods:['GET'])]
    public function delete(Recipe $recipe, EntityManagerInterface $em, Request $request): Response
    {
        $em->remove($recipe);
        $em->flush();


        $this->addFlash(
            'success',
            'Votre recette a été supprimée avec succès !'
        );

        return $this->redirectToRoute('app_recipe_index');
    }


}
