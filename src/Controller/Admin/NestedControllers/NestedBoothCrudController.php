<?php

namespace App\Controller\Admin\NestedControllers;

use App\Admin\Field\EquipmentField;
use App\Controller\Admin\Traits\DisableAllActions;
use App\Entity\Booth;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

final class NestedBoothCrudController extends AbstractCrudController
{
    use DisableAllActions;

    public static function getEntityFqcn(): string
    {
        return Booth::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Booth name'),
            Field\NumberField::new('maxNumberOfParticipants'),
            EquipmentField::new('availableEquipment'),
        ];
    }
}
