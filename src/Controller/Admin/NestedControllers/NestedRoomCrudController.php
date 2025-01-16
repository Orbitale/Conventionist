<?php

namespace App\Controller\Admin\NestedControllers;

use App\Controller\Admin\Traits\DisableAllActions;
use App\Entity\Room;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class NestedRoomCrudController extends AbstractCrudController
{
    use DisableAllActions;

    public static function getEntityFqcn(): string
    {
        return Room::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Room name'),
            Field\CollectionField::new('tables')->useEntryCrudForm(NestedTableCrudController::class),
        ];
    }
}
