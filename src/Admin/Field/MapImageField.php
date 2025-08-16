<?php

namespace App\Admin\Field;

use App\Form\Type\MapImageType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Image;

class MapImageField implements FieldInterface
{
    use FieldTrait;

    public const array IMAGES_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
    ];

    public const string PATH_FROM_PUBLIC_DIR = '/uploads/maps';

    public static function new(string $propertyName, ?string $label = null): FieldInterface|ImageField
    {
        return ImageField::new('mapImage', 'Map or plan')
            ->setUploadDir('/public/'.self::PATH_FROM_PUBLIC_DIR)
            ->setHelp('map_image.form.help')
            ->setFormTypeOption('block_prefix', 'map_image')
            ->addFormTheme('admin/form/map_image_form.html.twig')
            ->setUploadedFileNamePattern(fn (UploadedFile $file) => self::PATH_FROM_PUBLIC_DIR.'/'.\basename($file->getClientOriginalPath(), $file->getClientOriginalExtension()).uniqid('', true).'.'.$file->getClientOriginalExtension())
            ->setFileConstraints(new Image(
                maxSize: '5M',
                mimeTypes: self::IMAGES_TYPES,
                mimeTypesMessage: 'Please upload a valid image (JPEG, PNG, GIF, SVG).',
            ))
        ;
    }
}
