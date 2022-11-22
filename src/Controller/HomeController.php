<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home_index')]
    public function index(RecipeRepository $repo): Response
    {
        return $this->render('pages/home.html.twig', [
            'recipes'=> $repo->findPublicRecipe(3)
        ]);
    }
}
