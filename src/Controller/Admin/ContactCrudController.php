<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ContactCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Demandes de contact')
            ->setEntityLabelInSingular('Demande de contact')
            ->setPageTitle("index", "myRecipe - Administation des contacts")
            ->setPaginatorPageSize(5)
            ->addFormTheme('@FOSCKEditor/Form/ckeditor_widget.html.twig');
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnIndex(),
            TextField::new('fullname'),
            TextField::new('email'),
            TextareaField::new('message')
                ->setFormType(CKEditorType::class)
                ->hideOnIndex(),
            DateTimeField::new('createdAt')->setFormTypeOption('disabled', 'disabled')
        ];
    }
}
