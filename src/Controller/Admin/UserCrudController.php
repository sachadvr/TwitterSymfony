<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('email', 'Email'),
            TextField::new('username', 'Utilisateur'),
            TextField::new('image_path', 'Lien de l\'image'),
            ChoiceField::new('verified', 'Compte vérifié')
                ->setChoices([
                    'Oui' => 1,
                    'Non' => 0,
                ])
                ->renderExpanded(true),
             ChoiceField::new('darkmode', 'Mode Nuit')
                ->setChoices([
                    'Oui' => 1,
                    'Non' => 0,
                ])
                ->renderExpanded(true),
            ChoiceField::new('roles', 'Permissions')
                ->allowMultipleChoices()
                ->setChoices([
                    'Utilisateur' => 'ROLE_USER',
                    'Administrateur' => 'ROLE_ADMIN',
                    'Banni' => 'ROLE_BANNED',
                ])
                ->renderExpanded(true),
            TextField::new('password', 'Mot de passe')
            ->onlyOnForms()
            ,
        ];
    }
    
}
