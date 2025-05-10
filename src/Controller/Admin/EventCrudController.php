<?php

namespace App\Controller\Admin;

use App\Admin\Field\AssociationField;
use App\Entity\Event;
use App\Entity\User;
use App\Security\Voter\EventVoter;
use Doctrine\ORM\EntityManagerInterface;
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

final class EventCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Event::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, EventVoter::CAN_DELETE_EVENT);

        return $actions;
    }

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);

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

    /**
     * @param Event $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            /** @var User $user */
            $user = $this->getUser();
            $entityInstance->addCreator($user);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            Field\FormField::addColumn(6),
            Field\FormField::addFieldset('General'),
            Field\TextField::new('name')->setEditInPlace(['index', 'detail']),
            Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex(),
            Field\AssociationField::new('venue')->setRequired(true),
            AssociationField::new('creators')
                ->setHelp('admin.field.creators.help'),
            Field\BooleanField::new('isOnlineEvent')->renderAsSwitch(false),

            Field\FormField::addColumn(6),
            Field\FormField::addFieldset('Dates'),
            Field\DateTimeField::new('startsAt'),
            Field\DateTimeField::new('endsAt'),
            Field\FormField::addFieldset('Registration'),
            Field\BooleanField::new('allowActivityRegistration')->renderAsSwitch(false)
                ->setHelp('admin.field.allow_activity_registration.help'),
            Field\BooleanField::new('allowAttendeeRegistration')->renderAsSwitch(false)
                ->setHelp('admin.field.allow_attendee_registration.help'),
        ];
    }
}
