<?php

namespace App\Controller\Admin;

use App\Admin\Field as CustomFields;
use App\Controller\Admin\NestedControllers\NestedFloorCrudController;
use App\Entity\Venue;
use App\Security\Voter\VenueVoter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

final class VenueCrudController extends AbstractCrudController
{
    use GenericCrudMethods {configureCrud as baseConfigureCrud;}

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Venue::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $this
            ->baseConfigureCrud($crud)
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, VenueVoter::CAN_DELETE_VENUE);

        $actions->add(
            Crud::PAGE_DETAIL,
            Action::new('copy')
                ->linkToCrudAction('copy')
                ->setHtmlAttributes(['data-confirm' => $this->translator->trans('venue.copy.confirm')])
        );

        return $actions;
    }

    #[AdminAction(routePath: '/copy/{entityId}', routeName: 'venue_copy', methods: ['GET', 'POST'])]
    public function copy(AdminContext $context): RedirectResponse
    {
        $venue = $context->getEntity()->getInstance();

        if (!$venue) {
            throw new NotFoundHttpException();
        }

        if (!$venue instanceof Venue) {
            throw new BadRequestHttpException();
        }

        $newVenue = $venue->clone();
        $newVenue->setName($newVenue->getName().$this->translator->trans('venue.copy.suffix'));

        $this->persistEntity($this->container->get('doctrine')->getManagerForClass($context->getEntity()->getFqcn()), $newVenue);

        return $this->redirectToRoute('admin_venue_edit', ['entityId' => $newVenue->getId()]);
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        $qb
            ->leftJoin('entity.floors', 'floors')
            ->addSelect('floors')
        ;

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.creators', 'creators')
            ->addSelect('creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addColumn(6, 'Basic information')->hideOnDetail();
        yield Field\TextField::new('name');
        yield CustomFields\AssociationCollectionField::new('floors', null, NestedFloorCrudController::class, FloorCrudController::class);
        yield Field\TextField::new('address')->hideOnForm()->hideOnDetail();
        yield Field\TextField::new('address1')->hideOnIndex();
        yield Field\TextField::new('address2')->hideOnIndex();
        yield Field\TextField::new('state')->hideOnIndex();
        yield Field\TextField::new('zipCode')->hideOnIndex();
        yield Field\TextField::new('city')->hideOnIndex();
        yield Field\TextField::new('country')->hideOnIndex();
        yield Field\TextField::new('latitude')->hideOnIndex();
        yield Field\TextField::new('longitude')->hideOnIndex();
        yield Field\DateTimeField::new('createdAt')->hideOnForm();
        yield Field\FormField::addColumn(6, '')->hideOnDetail();
        yield CustomFields\MapImageField::new('mapImage', 'Map or plan');
        yield Field\NumberField::new('mapWidth', 'Map width')->onlyOnDetail();
        yield Field\NumberField::new('mapHeight', 'Map height')->onlyOnDetail();
        yield CustomFields\PlanDispositionField::newForEntity('venue')->onlyOnDetail();
    }
}
