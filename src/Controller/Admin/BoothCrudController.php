<?php

namespace App\Controller\Admin;

use App\Admin\Field\EquipmentField;
use App\Admin\Field\NumberField;
use App\Entity\Booth;
use App\Repository\BoothRepository;
use App\Security\Voter\VenueVoter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Factory\AdminContextFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\ControllerFactory;
use EasyCorp\Bundle\EasyAdminBundle\Factory\FieldFactory;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BoothCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public function __construct(
        private readonly BoothRepository $boothRepository,
        private readonly AdminContextFactory $contextFactory,
        private readonly ControllerFactory $controllerFactory,
        private readonly FieldFactory $fieldFactory,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Booth::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions->add(
            Crud::PAGE_INDEX,
            Action::new('editInPlace')
                ->linkToCrudAction('editInPlace')
                ->addCssClass('visually-hidden')
                ->createAsGlobalAction()
        );

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, VenueVoter::CAN_DELETE_VENUE);

        return $actions;
    }

    public function editInPlace(AdminContext $baseContext): Response
    {
        dump($baseContext);
        $request = $baseContext->getRequest();

        $context = $this->contextFactory->create(
            request: $request,
            dashboardController: $this->controllerFactory->getDashboardControllerInstance($baseContext->getDashboardControllerFqcn(), $request),
            crudController: $this,
            actionName: 'list',
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

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.room', 'room')
            ->innerJoin('room.floor', 'floor')
            ->innerJoin('floor.venue', 'venue')
            ->innerJoin('venue.creators', 'creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name', 'Booth name or number');
        yield Field\AssociationField::new('room')->setDisabled($pageName === Crud::PAGE_EDIT);
        yield NumberField::new('maxNumberOfParticipants');
        yield EquipmentField::new('availableEquipment')
            ->setCustomOption('translateKey', true)
            ->setTemplatePath('admin/fields/field.array.html.twig');
    }
}
