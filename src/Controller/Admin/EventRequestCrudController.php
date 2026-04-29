<?php

namespace App\Controller\Admin;

use App\Entity\EventRequest;
use App\Enum\EventRequestStatus;
use App\Enum\EventRequestType;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<EventRequest>
 */
final class EventRequestCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return EventRequest::class;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

        if ($this->isGranted('ROLE_ADMIN')) {
            return $qb;
        }

        $qb->innerJoin('entity.event', 'event')
            ->leftJoin('event.creators', 'eventCreators')
            ->andWhere('entity.user = :me OR eventCreators = :me')
            ->setParameter('me', $this->getUser())
        ;

        return $qb;
    }

    public function configureFields(string $pageName): iterable
    {
        yield Field\AssociationField::new('user');
        yield Field\AssociationField::new('event');
        yield Field\ChoiceField::new('requestType')
            ->setChoices(EventRequestType::cases())
            ->renderAsBadges();
        yield Field\ChoiceField::new('status')
            ->setChoices(EventRequestStatus::cases())
            ->renderAsBadges();
        yield Field\TextareaField::new('message')->hideOnIndex()->setRequired(false);
        yield Field\DateTimeField::new('reviewedAt')->hideOnForm()->hideOnIndex();
        yield Field\AssociationField::new('reviewedBy')->hideOnForm()->hideOnIndex();
    }
}
