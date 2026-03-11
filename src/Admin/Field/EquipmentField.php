<?php

namespace App\Admin\Field;

use App\Form\Type\AssociativeArrayType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Contracts\Translation\TranslatableInterface;

final class EquipmentField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null): CollectionField
    {
        return CollectionField::new($propertyName, $label)
            ->setFormType(AssociativeArrayType::class)
            ->setFormTypeOption('entry_options', [
                'label' => false,
                'key_options' => [
                    'label' => 'equipment.name',
                ],
                'value_type' => NumberType::class,
                'value_options' => [
                    'html5' => true,
                    'label' => 'equipment.quantity',
                    'attr' => ['min' => 0],
                ],
            ])
            ->setEntryToStringMethod(static function ($entry) {
                return $entry['item_key'] ?? '';
            })
            ->setEntryIsComplex()
        ;
    }
}
