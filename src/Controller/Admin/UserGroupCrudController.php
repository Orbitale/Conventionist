<?php

namespace App\Controller\Admin;

use App\Admin\Field\AssociationField;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Security\Voter\UserGroupVoter;
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

/**
 * @extends AbstractCrudController<UserGroup>
 */
final class UserGroupCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return UserGroup::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $this->checkParent();

        $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->setPermission(Action::DELETE, UserGroupVoter::CAN_DELETE_USER_GROUP);

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
     * @param UserGroup $entityInstance
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
            Field\TextField::new('name'),
            Field\TextField::new('slug')->hideOnForm(),
            Field\AssociationField::new('organization')->setRequired(true),
            Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex(),
            AssociationField::new('creators')
                ->setHelp('admin.field.creators.help'),
        ];
    }
}
