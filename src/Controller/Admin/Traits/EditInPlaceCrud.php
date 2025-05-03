<?php

namespace App\Controller\Admin\Traits;

use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\ControllerFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FieldFactory;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Service\Attribute\Required;

trait EditInPlaceCrud
{
    private readonly AdminContextFactory $contextFactory;
    private readonly ControllerFactory $controllerFactory;
    private readonly FieldFactory $fieldFactory;

    #[Required]
    public function setContextFactory(AdminContextFactory $contextFactory)
    {
        $this->contextFactory = $contextFactory;
    }

    #[Required]
    public function setControllerFactory(ControllerFactory $controllerFactory)
    {
        $this->controllerFactory = $controllerFactory;
    }

    #[Required]
    public function setFieldFactory(FieldFactory $fieldFactory)
    {
        $this->fieldFactory = $fieldFactory;
    }

    public function addEditInPlaceAction(Actions $actions): void
    {
        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('editInPlace')
                ->linkToCrudAction('editInPlace')
                ->addCssClass('visually-hidden')
                ->createAsGlobalAction()
        );
    }

    public function editInPlace(AdminContext $baseContext): Response
    {
        dump($baseContext);
        $request = $baseContext->getRequest();

        $context = $this->contextFactory->create(
            request: $request,
            dashboardController: $this->controllerFactory->getDashboardControllerInstance($baseContext->getDashboardControllerFqcn(), $request),
            crudController: $this,
            actionName: 'editInPlace',
        );
        $request->attributes->set(EA::CONTEXT_REQUEST_ATTRIBUTE, $context);
        dump(['context' => $context, 'baseContext' => $baseContext]);

        $fields = $context->getEntity()->getFields() ?: FieldCollection::new([]);
        $this->fieldFactory->processFields($context->getEntity(), $fields);
        if (!$fields || !$fields->count()) {
            throw new NotFoundHttpException('No fields found in this entity.');
        }

        $input = $context->getRequest()->getContent();
        if (!$input) {
            throw new BadRequestHttpException('No content.');
        }
        try {
            $json = \json_decode($input, true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable $jsonError) {
            throw new BadRequestHttpException('Invalid JSON input.', $jsonError);
        }
        if (!\is_array($json)) {
            throw new BadRequestHttpException('Invalid input: expected an array.');
        }
        if (\count($json) !== 1) {
            throw new BadRequestHttpException('Invalid input: only one field at a time is allowed.');
        }

        $propertyName = \key($json);
        if (!$propertyName) {
            throw new BadRequestHttpException('Invalid input: no property to update was set.');
        }

        $id = $context->getRequest()->get(EA::ENTITY_ID);
        if (!$id) {
            throw new NotFoundHttpException(\sprintf('The "%s" query parameter is empty.', EA::ENTITY_ID));
        }

        /** @var ManagerRegistry $registry */
        $registry = $this->container->get('doctrine');
        $em = $registry->getManagerForClass(self::getEntityFqcn());
        if (!$em) {
            throw new \RuntimeException('No entity manager for this entity class.');
        }
        $object = $registry->getRepository(self::getEntityFqcn())->find($id);
        if (!$object) {
            throw new NotFoundHttpException('No Entity found with this ID.');
        }

        $fieldDto = $fields->get($propertyName);
        if (!$fieldDto) {
            throw new NotFoundHttpException(\sprintf('No property "%s" in entity.', $propertyName));
        }

        $formBuilder = $this->createFormBuilder($object);
        $formBuilder->add($propertyName, $fieldDto->getFormType(), $fieldDto->getFormTypeOptions());

        $form = $formBuilder->getForm();

        $form->submit($json);
        if ($form->isSubmitted() && $form->isValid()) {
            return new Response('Ok!');
        }

        return new Response('Not valid!');
    }

}
