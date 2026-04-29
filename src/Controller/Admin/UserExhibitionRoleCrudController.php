<?php

namespace App\Controller\Admin;

use App\Entity\UserExhibitionRole;
use App\Enum\ExhibitionRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<UserExhibitionRole>
 */
final class UserExhibitionRoleCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return UserExhibitionRole::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\AssociationField::new('user')->setRequired(true),
            Field\AssociationField::new('event')->setRequired(true),
            Field\ChoiceField::new('role')
                ->setChoices(ExhibitionRole::cases())
                ->setRequired(true),
            Field\DateTimeField::new('scopedAt')->hideOnForm(),
        ];
    }
}
