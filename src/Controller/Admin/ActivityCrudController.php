<?php

namespace App\Controller\Admin;

use App\Admin\Field\EquipmentField;
use App\Entity\Activity;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field;

final class ActivityCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Activity::class;
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
     * @param Activity $entityInstance
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
        yield Field\TextField::new('name');
        yield Field\NumberField::new('maxNumberOfParticipants')->setRequired(false)->hideOnIndex();
        yield Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex();
        yield Field\AssociationField::new('creators')->setHelp('The users that are allowed to manage this activity. Note: you will always be included in this list, even if you remove yourself from it.');
        yield EquipmentField::new('neededEquipment');
    }
}
