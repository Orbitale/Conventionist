<?php

namespace App\Form\Factory;

use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

readonly class AdminSingleFieldFormFactory
{
    public function __construct(
        private FormFactoryInterface $formFactory
    ) {}

    public function createBuilder(EntityDto $entityDto, FieldDto $fieldDto): FormBuilderInterface
    {
        return $this->formFactory->createNamedBuilder(
            $entityDto->getName() . '-' . $fieldDto->getProperty(),
            $fieldDto->getFormType(),
            $fieldDto->getValue(),
            $fieldDto->getFormTypeOptions(),
        );
    }
}
