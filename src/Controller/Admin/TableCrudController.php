<?php

namespace App\Controller\Admin;

use App\Entity\Table;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class TableCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Table::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Table name or number'),
            Field\AssociationField::new('room')->setDisabled($pageName === Crud::PAGE_EDIT),
            Field\NumberField::new('maxNumberOfParticipants'),
        ];
    }
}
