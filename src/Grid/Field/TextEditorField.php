<?php

namespace App\Grid\Field;

use Sylius\Bundle\GridBundle\Builder\Field\Field;
use Sylius\Bundle\GridBundle\Builder\Field\FieldInterface;

class TextEditorField
{
    public static function create(string $name): FieldInterface
    {
        return Field::create($name, 'texteditor')
            ->setSortable(false)
            ->setOptions(['template' => 'templates/grid/field/text-editor.field.html.twig'])
        ;
    }
}
