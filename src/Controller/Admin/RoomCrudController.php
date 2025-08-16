<?php

namespace App\Controller\Admin;

use App\Admin\Field as CustomFields;
use App\Controller\Admin\NestedControllers\NestedBoothCrudController;
use App\Entity\Room;
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

final class RoomCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Room::class;
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

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.floor', 'floor')
            ->innerJoin('floor.venue', 'venue')
            ->innerJoin('venue.creators', 'creators')
            ->andWhere('creators IN (:creator)')
            ->setParameter('creator', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\FormField::addColumn(6, 'Basic information');
        yield Field\TextField::new('name', 'Room name');
        yield Field\AssociationField::new('floor')->setDisabled($pageName === Crud::PAGE_EDIT);
        yield CustomFields\AssociationCollectionField::new('booths', null, NestedBoothCrudController::class, BoothCrudController::class);
        yield Field\FormField::addColumn(6, '');
        yield CustomFields\MapImageField::new('mapImage', 'Map or plan');
        yield Field\NumberField::new('mapWidth', 'Map width')->hideOnForm()->hideOnIndex();
        yield Field\NumberField::new('mapHeight', 'Map height')->hideOnForm()->hideOnIndex();
    }
}
