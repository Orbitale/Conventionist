<?php

namespace App\Controller\Admin;

use App\Entity\HasNestedRelations;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait GenericCrudMethods
{
    public function configureCrud(Crud $crud): Crud
    {
        $this->checkParent();

        $crud->showEntityActionsInlined();

        return $crud;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions->add(Crud::PAGE_INDEX, Action::DETAIL);

        return $actions;
    }

    public function getRedirectResponseAfterSave(AdminContext $context, string $action): RedirectResponse
    {
        $this->checkParent();

        $this->addFlashAfterSave($context, $action);

        return parent::getRedirectResponseAfterSave($context, $action);
    }

    public function addFlashAfterSave(AdminContext $context, string $action): void
    {
        if (Action::EDIT === $action) {
            /** @var object $item */
            $item = $context->getEntity()->getInstance();
            $this->addFlash('success', \sprintf('Successfully updated "%s"!', $item));
        }

        if (Action::NEW === $action) {
            /** @var object $item */
            $item = $context->getEntity()->getInstance();
            $this->addFlash('success', \sprintf('Successfully created "%s"!', $item));
        }

        if (Action::DELETE === $action) {
            /** @var object $item */
            $item = $context->getEntity()->getInstance();
            $this->addFlash('success', \sprintf('Successfully deleted "%s"!', $item));
        }
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $this->checkParent();

        return $this->addRootEntityToRelations(parent::createNewFormBuilder($entityDto, $formOptions, $context));
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $this->checkParent();

        return $this->addRootEntityToRelations(parent::createEditFormBuilder($entityDto, $formOptions, $context));
    }

    private function addRootEntityToRelations(FormBuilderInterface $builder): FormBuilderInterface
    {
        if (!\is_a(static::getEntityFqcn(), HasNestedRelations::class, true)) {
            return $builder;
        }

        return $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                $entity = $event->getForm()->getData();
                if (!($entity instanceof HasNestedRelations)) {
                    throw new \RuntimeException(\sprintf('Invalid usage of "%s". It must be associated with an entity instance that implements "%s".', static::class, HasNestedRelations::class));
                }
                $entity->refreshNestedRelations();
            })
        ;
    }

    private function checkParent(): void
    {
        if (!($this instanceof AbstractCrudController)) {
            throw new \RuntimeException(\sprintf('Invalid usage of "%s". It must extend "%s".', static::class, AbstractCrudController::class));
        }
    }
}
