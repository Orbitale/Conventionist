<?php

namespace App\Controller\Admin\NestedControllers;

use App\Controller\Admin\Traits\DisableAllActions;
use App\Entity\Table;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class NestedTableCrudController extends AbstractCrudController
{
    use DisableAllActions;

    public static function getEntityFqcn(): string
    {
        return Table::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Table name'),
            Field\NumberField::new('maxNumberOfParticipants'),
        ];
    }
}
