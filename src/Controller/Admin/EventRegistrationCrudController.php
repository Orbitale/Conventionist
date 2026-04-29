<?php

namespace App\Controller\Admin;

use App\Entity\EventRegistration;
use App\Enum\EventRegistrationStatus;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<EventRegistration>
 */
final class EventRegistrationCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return EventRegistration::class;
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
        yield Field\ChoiceField::new('status')
            ->setChoices(EventRegistrationStatus::cases())
            ->renderAsBadges();
        yield Field\DateTimeField::new('registeredAt')->hideOnForm();
        yield Field\DateTimeField::new('confirmedAt')->hideOnForm()->hideOnIndex();
        yield Field\TextareaField::new('notes')->hideOnIndex()->setRequired(false);
    }
}
