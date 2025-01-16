<?php

namespace App\Controller\Admin\NestedControllers;

use App\Controller\Admin\Traits\DisableAllActions;
use App\Entity\Floor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class NestedFloorCrudController extends AbstractCrudController
{
    use DisableAllActions;

    public static function getEntityFqcn(): string
    {
        return Floor::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Floor name'),
            Field\CollectionField::new('rooms')->useEntryCrudForm(NestedRoomCrudController::class),
        ];
    }
}
