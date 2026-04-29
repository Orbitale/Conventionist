<?php

namespace App\Controller\Admin;

use App\Entity\GameTheme;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<GameTheme>
 */
final class GameThemeCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return GameTheme::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name');
        yield Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex();
    }
}
