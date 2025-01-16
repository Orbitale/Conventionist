<?php

namespace App\Controller\Admin;

use App\Controller\Admin\NestedControllers\NestedRoomCrudController;
use App\Entity\Floor;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

class FloorCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Floor::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

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

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\TextField::new('name', 'Floor name'),
            Field\AssociationField::new('venue')->setDisabled($pageName === Crud::PAGE_EDIT),
            Field\CollectionField::new('rooms')->useEntryCrudForm(NestedRoomCrudController::class),
        ];
    }
}
