<?php

namespace App\Controller\Admin;

use App\Entity\TimeSlotCategory;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class TimeSlotCategoryCrudController extends AbstractCrudController
{
    use GenericCrudMethods ;

    public static function getEntityFqcn(): string
    {
        return TimeSlotCategory::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setEntityPermission('ROLE_ADMIN');
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name');
        yield Field\TextEditorField::new('description')->setRequired(false);
    }
}
