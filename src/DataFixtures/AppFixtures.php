<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{

    // Implementer FAKER
    /**
     * @var Generator
     */
    private Generator $faker;
    
    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }

    // Mettre en place les FIXTURES
    public function load(ObjectManager $manager): void
    {
        // Ingredient
        // Mettre dans un tableau la liste des ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {    
            $ingredient = new Ingredient();
            // Utiliser FAKER pour le nom de l'ingredient
            $ingredient->setName($this->faker->word())
            ->setPrice(mt_rand(0, 100));
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recette
        $recipes = [];
        for ($i=0; $i < 30; $i++) { 
            $recipe = new Recipe();
                $recipe->setName('recipe_' . $this->faker->word())
                ->setTime(mt_rand(0,1) == 1 ? mt_rand(1, 1440): null)
                ->setNbPeople(mt_rand(0,1) == 1 ? mt_rand(1, 50): null)
                ->setDifficulty(mt_rand(0,1) == 1 ? mt_rand(1, 5): null)
                ->setDescription($this->faker->sentence())
                ->setPrice(mt_rand(0,1) == 1 ? mt_rand(1, 1000): null)
                ->setIsFavorite(mt_rand(0,1) == 1 ? true : false);
                // On rajoute entre 5 et 15 ingredients pour chaque recette
                for ($k=0; $k < (mt_rand(5,15)); $k++) { 
                    $recipe->addIngredient($ingredients[mt_rand(1, count($ingredients) - 1)]);
                }
                $recipes[] = $recipe;
                $manager->persist($recipe);
        }
        $manager->flush();
    }
}
