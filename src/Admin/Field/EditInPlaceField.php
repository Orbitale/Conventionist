<?php

namespace App\Admin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

class EditInPlaceField implements FieldInterface
{
    use FieldTrait {
        setHtmlAttribute as baseSetHtmlAttribute;
    }

    public static function new(string $propertyName, $label = null, ?FieldInterface $internalField = null): EditInPlaceField
    {
        if (!$internalField) {
            throw new \InvalidArgumentException('Internal field is mandatory.');
        }

        $internalFieldDto = $internalField->getAsDto();

        $self = new self()
            ->setCustomOption('internalField', $internalField)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormType($internalFieldDto->getFormType())
            ->setFormTypeOptions($internalFieldDto->getFormTypeOptions())
            ->setCustomOption('editInPlace', true)
        ;

        $self->dto->setTemplatePath('admin/fields/field.edit_in_place.html.twig');

        return $self;
    }

    public function setTemplatePath(string $path): self
    {
        return $this;
    }

    public function setTemplateName(string $name): self
    {
        return $this;
    }
}
