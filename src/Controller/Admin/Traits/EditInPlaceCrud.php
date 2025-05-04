<?php

namespace App\Controller\Admin\Traits;

use App\Form\Factory\AdminSingleFieldFormFactory;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
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
    private readonly AdminSingleFieldFormFactory $formFactory;

    #[Required]
    public function setContextFactory(AdminContextFactory $contextFactory): void
    {
        $this->contextFactory = $contextFactory;
    }

    #[Required]
    public function setControllerFactory(ControllerFactory $controllerFactory): void
    {
        $this->controllerFactory = $controllerFactory;
    }

    #[Required]
    public function setFieldFactory(FieldFactory $fieldFactory): void
    {
        $this->fieldFactory = $fieldFactory;
    }

    #[Required]
    public function setFormFactory(AdminSingleFieldFormFactory $formFactory): void
    {
        $this->formFactory = $formFactory;
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
        $request = $baseContext->getRequest();

        $context = $this->contextFactory->create(
            request: $request,
            dashboardController: $this->controllerFactory->getDashboardControllerInstance($baseContext->getDashboardControllerFqcn(), $request),
            crudController: $this,
            actionName: 'editInPlace',
        );
        $request->attributes->set(EA::CONTEXT_REQUEST_ATTRIBUTE, $context);

        $entityDto = $context->getEntity();

        $fields = FieldCollection::new($this->configureFields(Crud::PAGE_INDEX));
        $this->fieldFactory->processFields($entityDto, $fields);
        if (!$fields->count()) {
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
        if (\count($json) !== 2) {
            throw new BadRequestHttpException('Invalid input: only one field at a time is allowed.');
        }

        $forProperty = $json;
        unset($forProperty['_token']);

        $fullKey = \key($json);
        if (!$fullKey) {
            throw new BadRequestHttpException('Invalid input: no property to update was set.');
        }
        $propertyName = \preg_replace(\sprintf('~^%s-(.+)$~isUu', $entityDto->getName()), '$1', $fullKey);

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

        $fieldDto = $fields->getByProperty($propertyName);
        if (!$fieldDto) {
            throw new NotFoundHttpException(\sprintf('No property "%s" in entity.', $propertyName));
        }

        /** @var FieldInterface|null $internalField */
        $internalField = $fieldDto->getCustomOption('internalField');
        if (!$internalField) {
            throw new NotFoundHttpException(\sprintf('Field "%s" does not have internal field. Did you forget to set the "internalField" custom option?', $propertyName));
        }

        $form = $this->formFactory->createBuilder($entityDto, $fieldDto)->getForm();

        $form->submit($json[$fullKey]);
        if ($form->isSubmitted() && $form->isValid()) {

            return $this->render($internalField->getTemplatePath(), ['field' => $fieldDto, 'entity' => $entityDto]);
        }

        return new Response('Not valid!'."\n".$form->getErrors(true));
    }
}
