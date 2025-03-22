<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField as BaseAssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class AssociationField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, $label = null): BaseAssociationField
    {
        return BaseAssociationField::new($propertyName)
            ->setTemplatePath('admin/fields/field.association_array.html.twig');
    }
}
