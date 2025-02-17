<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\RoleType;
use App\Mailer\UserMailer;
use App\Util\Urlizer;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

final class UsersCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordEncoder,
        private readonly UserMailer $mailer,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN')
            ->showEntityActionsInlined()
            ->setSearchFields(['id', 'username', 'email', 'roles'])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable('delete')
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    /**
     * @param User $entityInstance
     */
    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        foreach ($entityInstance->formNewRoles as $role) {
            $entityInstance->addRole($role);
        }

        parent::updateEntity($entityManager, $entityInstance);
    }

    /**
     * @param User $entityInstance
     */
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$hasPassword = $entityInstance->formNewPassword) {
            $entityInstance->setConfirmationToken(\str_replace('-', '', Uuid::v4()));
            $entityInstance->formNewPassword = \uniqid('', true);
        }
        $entityInstance->setPassword($this->passwordEncoder->hashPassword($entityInstance, $entityInstance->formNewPassword));
        $entityInstance->setEmailConfirmed(true);
        $entityInstance->eraseCredentials();

        foreach ($entityInstance->formNewRoles as $role) {
            $entityInstance->addRole($role);
        }

        // Causes the persist + flush
        parent::persistEntity($entityManager, $entityInstance);

        if (!$hasPassword) {
            // With no password, we send a "reset password" email to the user
            $this->mailer->sendResettingEmailMessage($entityInstance);
        }
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $builder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        $this->addUrlizingFormListener($builder);

        return $builder;
    }

    public function createEditFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $builder = parent::createEditFormBuilder($entityDto, $formOptions, $context);

        $this->addUrlizingFormListener($builder);

        return $builder;
    }

    public function addUrlizingFormListener(FormBuilderInterface $builder): void
    {
        $builder
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
                /** @var User $user */
                $user = $event->getForm()->getData();
                $user->setUsername(Urlizer::urlize($user->getUsername()));
                $user->setEmail(Urlizer::urlize($user->getEmail()));
            })
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $id = TextField::new('id', 'ID')->setDisabled()->hideOnForm();
        $username = TextField::new('username');
        $email = TextField::new('email');
        $plainPassword = Field::new('formNewPassword')->setHelp('admin.entities.users.password_help');
        $newRoles = CollectionField::new('formNewRoles', 'admin.roles.new')->setEntryType(RoleType::class);
        $roles = ArrayField::new('roles')->setTemplatePath('admin/fields/field.roles.html.twig');
        $emailConfirmed = BooleanField::new('emailConfirmed');
        $createdAt = DateTimeField::new('createdAt');
        $updatedAt = DateTimeField::new('updatedAt');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$username, $email, $roles, $emailConfirmed, $createdAt];
        }
        if (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $username, $email, $roles, $emailConfirmed, $createdAt, $updatedAt];
        }
        if (Crud::PAGE_NEW === $pageName) {
            return [$username, $email, $plainPassword, $newRoles];
        }
        if (Crud::PAGE_EDIT === $pageName) {
            return [$username->setDisabled(), $email->setDisabled(), $emailConfirmed, $newRoles];
        }

        throw new \RuntimeException(\sprintf('Unsupported CRUD action "%s".', $pageName));
    }
}
