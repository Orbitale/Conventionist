<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class AssociationCollectionField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param class-string<AbstractCrudController>|null $entryCrudForm
     * @param class-string<AbstractCrudController>|null $editController
     */
    public static function new(string $propertyName, TranslatableInterface|string|bool|null $label = null, ?string $entryCrudForm = null, ?string $editController = null): CollectionField
    {
        $field = CollectionField::new($propertyName)
            ->setTemplatePath('admin/fields/field.association_array.html.twig');

        if ($entryCrudForm) {
            $field->useEntryCrudForm($entryCrudForm);
        }

        if ($editController) {
            $field->setCustomOption('editController', $editController);
        }

        if (
            $entryCrudForm
            && \method_exists($entryCrudForm::getEntityFqcn(), 'getName')
        ) {
            $field->setEntryToStringMethod(static function (mixed $object) {
                return $object?->getName() ?: '';
            });
        }

        return $field;
    }
}
