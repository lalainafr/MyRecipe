<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    // Implementer FAKER
    /**
     * @var Generator
     */
    private Generator $faker;
    
    private UserPasswordHasherInterface $hasher;

    public function __construct( UserPasswordHasherInterface $hasher){
        $this->faker = Factory::create('fr_FR');
        $this->hasher = $hasher;
    }

    // Mettre en place les FIXTURES
    public function load(ObjectManager $manager): void
    {

         // User
        $users = [];    
        for ($i=0; $i < 10 ; $i++) { 
            
            $user = new User();
            $user->setFullName($this->faker->name())
            ->setPseudo($this->faker->firstName())
            ->setEmail($this->faker->email())
            ->setRoles(['ROLE_USER']);
            
            $hashPassword = $this->hasher->hashPassword($user, 'password');
            $user->setPassword($hashPassword);

            $users[] = $user;
            $manager->persist($user);
        }

        // Ingredient
        // Mettre dans un tableau la liste des ingredients
        $ingredients = [];
        for ($i = 0; $i < 50; $i++) {    
            $ingredient = new Ingredient();
            // Utiliser FAKER pour le nom de l'ingredient
            $ingredient->setName('ingredient_'. strtoupper($this->faker->word()))
            ->setPrice(mt_rand(0, 100))
            ->setUser($users[mt_rand(0, count($users) - 1)]);
            $ingredients[] = $ingredient;
            $manager->persist($ingredient);
        }

        // Recette
        $recipes = [];
        for ($i=0; $i < 30; $i++) { 
            $recipe = new Recipe();
                $recipe->setName('recette_' . strtoupper($this->faker->word()))
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
