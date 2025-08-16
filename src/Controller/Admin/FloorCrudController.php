<?php

namespace App\Controller\Admin;

use App\Admin\Field as CustomFields;
use App\Controller\Admin\NestedControllers\NestedRoomCrudController;
use App\Entity\Floor;
use App\Repository\VenueRepository;
use App\Security\Voter\VenueVoter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

final class FloorCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Floor::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(EntityFilter::new('venue')
        ->setFormTypeOption('value_type_options', [
            'query_builder' => function (VenueRepository $repository) {
                $qb = $repository->createQueryBuilder('entity');

                $qb->leftJoin('entity.floors', 'floors')->addSelect('floors');

                if ($this->isGranted('ROLE_ADMIN')) {
                    return $qb;
                }

                $qb->innerJoin('entity.creators', 'creators')
                    ->addSelect('creators')
                    ->andWhere('creators IN (:creator)')
                    ->setParameter('creator', $this->getUser())
                ;
                return $qb;
            },
        ]));
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, VenueVoter::CAN_DELETE_VENUE);

        return $actions;
    }

    public function updateSelectionQueryBuilder(QueryBuilder $qb): QueryBuilder
    {
        $qb
            ->innerJoin('entity.venue', 'venue')
            ->addSelect('venue')
            ->leftJoin('entity.rooms', 'rooms')
            ->addSelect('rooms')
        ;

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('venue.creators', 'creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return $this->updateSelectionQueryBuilder(parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters));
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\TextField::new('name', 'Floor name');
        yield Field\AssociationField::new('venue')->setDisabled($pageName === Crud::PAGE_EDIT);
        yield CustomFields\AssociationCollectionField::new('rooms', null, NestedRoomCrudController::class, RoomCrudController::class);
    }
}
