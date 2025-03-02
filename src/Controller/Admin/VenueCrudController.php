<?php

namespace App\Controller\Admin;

use App\Admin\Field\AssociationCollectionField;
use App\Controller\Admin\NestedControllers\NestedFloorCrudController;
use App\Entity\Venue;
use App\Security\Voter\VenueVoter;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

final class VenueCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Venue::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, VenueVoter::CAN_DELETE_VENUE);

        return $actions;
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
        return [
            Field\TextField::new('name'),
            AssociationCollectionField::new('floors', null, NestedFloorCrudController::class, FloorCrudController::class),
            Field\TextField::new('address')->hideOnForm(),
            Field\TextField::new('address1')->hideOnIndex(),
            Field\TextField::new('address2')->hideOnIndex(),
            Field\TextField::new('state')->hideOnIndex(),
            Field\TextField::new('zipCode')->hideOnIndex(),
            Field\TextField::new('city')->hideOnIndex(),
            Field\TextField::new('country')->hideOnIndex(),
            Field\TextField::new('latitude')->hideOnIndex(),
            Field\TextField::new('longitude')->hideOnIndex(),
        ];
    }
}
