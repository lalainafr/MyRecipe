<?php

namespace App\Controller;

use App\Entity\Mark;
use App\Entity\Recipe;
use App\Form\MarkType;
use DateTimeImmutable;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
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

    #[Route('/recette/community', name: 'app_recipe_community', methods:['GET'])]
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
    #[Route('/recette/{id}', name: 'app_recipe_show', methods:['GET', 'POST'])]
    public function show(Recipe $recipe, Request $request, EntityManagerInterface $em, MarkRepository $repo): Response
    {
        $mark = new Mark();
        $form = $this->createForm(MarkType::class, $mark);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $mark->setUser($this->getUser())
                ->setRecipe($recipe);

                $existingNote = $repo->findOneBy(
                    [
                        'user' => $this->getUser(),
                        'recipe' => $recipe
                    ]
                );
                if(!$existingNote){
                    // $existingNote->setMark($form->getData()->getMark());
                    $em->persist($mark);
                    $this->addFlash(
                        'success',
                        'Votre note a été rajourtée avec succès !'
                    );
                } else {
                    $this->addFlash(
                        'warning',
                        'Désole... vous avez déjà noté cette recette  !'
                    );
                }
                $em->flush();
                return $this->redirectToRoute('app_recipe_show', ['id' => $recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView()
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

    #[Security("is_granted('ROLE_USER') and user === recipe.getUser()")]
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
