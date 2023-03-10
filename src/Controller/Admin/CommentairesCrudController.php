<?php

namespace App\Controller\Admin;

use App\Entity\Commentaires;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommentairesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commentaires::class;
    }

    public function configureFields(string $pageName): iterable
    {
         return [
            TextField::new('contenu', 'Contenu'),
            AssociationField::new('LinkedPost', 'Post lié')->setCrudController(PostCrudController::class)->renderAsNativeWidget()->setDisabled(),
            AssociationField::new('createdBy', 'Créé par')->setCrudController(UserCrudController::class)->renderAsNativeWidget(),
        ];
    }
}
