<?php

namespace App\Twig\Extension;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Provider\AdminContextProviderInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdminFieldExtension extends AbstractExtension
{
    public function __construct(
        private readonly FormFactoryInterface $formFactory,
        private readonly AdminUrlGenerator $urlGenerator
    ) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('create_field_form', [$this, 'createFieldForm']),
        ];
    }

    public function createFieldForm(AdminContextProviderInterface $context, EntityDto $entity, FieldDto $fieldDto): FormInterface
    {
        $options = $fieldDto->getFormTypeOptions();

        $builder = $this->formFactory->createNamedBuilder(
            $fieldDto->getProperty(),
            $fieldDto->getFormType(),
            $fieldDto->getValue(),
            $options,
        );

        $this->urlGenerator
            ->unsetAll()
            ->setDashboard($context->getDashboardControllerFqcn())
            ->setController($context->getCrud()?->getControllerFqcn())
            ->setEntityId($entity->getPrimaryKeyValue())
            ->setAction('editInPlace')
        ;
        $url = $this->urlGenerator->generateUrl();

        $builder->setAction($url);

        return $builder->getForm();
    }
}
