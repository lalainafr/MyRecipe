<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Ingredient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RecipeType extends AbstractType
{
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
            ->add('ingredient', EntityType::class, [
                'choice_label' => 'name',
                'class' => Ingredient::class,
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
