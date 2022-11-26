<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullname', TextType::class, [
                'label' => 'Nom / Prenom'  
                ])
                ->add('email', EmailType::class, [
                'label' => 'Email'  
                
                ])
                ->add('subject', TextType::class, [
                    'label' => 'Objet'  
                    ])
                ->add('message', TextType::class, [
                        'label' => 'Message'  
                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre', 
                'attr' => [
                    'class' => 'btn btn-primary'
                ]       
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
