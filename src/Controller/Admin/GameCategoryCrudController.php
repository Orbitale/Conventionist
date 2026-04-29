<?php

namespace App\Controller\Admin;

use App\Entity\GameCategory;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<GameCategory>
 */
final class GameCategoryCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return GameCategory::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name');
        yield Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex();
    }
}
