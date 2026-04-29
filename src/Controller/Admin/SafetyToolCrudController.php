<?php

namespace App\Controller\Admin;

use App\Entity\SafetyTool;
use App\Enum\SafetyToolType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<SafetyTool>
 */
final class SafetyToolCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return SafetyTool::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name');
        yield Field\ChoiceField::new('type')->setChoices(SafetyToolType::cases());
        yield Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex();
        yield Field\TextareaField::new('contentWarnings')->setRequired(false)->hideOnIndex();
    }
}
