<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class PlanDispositionField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): FieldInterface
    {
        throw new \RuntimeException(\sprintf('You can create a "%s" field only by using the "%s" static method.',
        __CLASS__, 'newForEntity'));
    }

    public static function newForEntity(string $entityName): Field
    {
        return Field::new('planDisposition', 'Plan disposition')
            ->setVirtual(true)
            ->setTemplatePath('admin/fields/field.plan_disposition.html.twig')
            ->setCustomOption('entity_name', $entityName)
            ->hideOnForm()
            ->hideOnIndex()
        ;
    }
}
