<?php

namespace App\Controller\Admin;

use App\Entity\UserGroupMembership;
use App\Enum\UserGroupRole;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<UserGroupMembership>
 */
final class UserGroupMembershipCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return UserGroupMembership::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\AssociationField::new('user')->setRequired(true),
            Field\AssociationField::new('userGroup')->setRequired(true),
            Field\ChoiceField::new('role')
                ->setChoices(UserGroupRole::cases())
                ->setRequired(true),
            Field\DateTimeField::new('joinedAt')->hideOnForm(),
        ];
    }
}
