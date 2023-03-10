<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Post::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Textfield::new('contenu', 'Contenu'),
            DateField::new('createdAt', 'Date de création'),
            DateField::new('lastModified', 'Dernière intéraction'),
            AssociationField::new('createdBy', 'Créé par')->setCrudController(UserCrudController::class)->renderAsNativeWidget(),
            AssociationField::new('retweet', 'Retweet')->setCrudController(PostCrudController::class),
            AssociationField::new('likes', 'Likes')->setCrudController(PostCrudController::class),
        ];
    }
}
