<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField as BaseNumberField;

class NumberField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, $label = null): BaseNumberField
    {
        return BaseNumberField::new($propertyName, $label)
            ->setCustomOption('editInPlace', true)
            ->setTemplatePath('admin/fields/field.number.html.twig')
        ;
    }
}
