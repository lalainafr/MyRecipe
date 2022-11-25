<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RecipeType extends AbstractType
{
    
    private $token;

    public function __construct(TokenStorageInterface $token)
    {
    $this->token = $token;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('time')
            ->add('nbPeople')
            ->add('difficulty')
            ->add('description')
            ->add('price')
            ->add('isFavorite')
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image de la recette'
            ])
            ->add('ingredient', EntityType::class, [
                'choice_label' => 'name',
                'class' => Ingredient::class,
                // DQL sur le repo Ingredient
                'query_builder' => function(IngredientRepository $r) {
                    return $r->createQueryBuilder('i')
                        ->where('i.user = :user')
                        // l'utilisateur connecté 
                        ->setParameter('user', $this->token->getToken()->getUser() )
                        ->orderBy('i.name' , 'ASC');
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('updateAt', DateType::class, [
                'label' => 'Date de modification',
                'widget' => 'single_text',
                'disabled' => true,
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Valider',
                    'attr' => [
                        'class' => 'btn btn-primary'
                    ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
