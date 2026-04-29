<?php

namespace App\Controller\Admin;

use App\Admin\Field\AssociationField;
use App\Entity\Game;
use App\Entity\User;
use App\Enum\GameComplexity;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field;

/**
 * @extends AbstractCrudController<Game>
 */
final class GameCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    /**
     * @param Game $entityInstance
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
        yield Field\TextField::new('title');
        yield Field\TextField::new('publisher')->setRequired(false)->hideOnIndex();
        yield Field\NumberField::new('minPlayers')->setRequired(false)->hideOnIndex();
        yield Field\NumberField::new('maxPlayers')->setRequired(false)->hideOnIndex();
        yield Field\NumberField::new('durationMinutes')->setRequired(false)->hideOnIndex();
        yield Field\ChoiceField::new('complexity')
            ->setChoices(GameComplexity::cases())
            ->setRequired(false)
            ->hideOnIndex();
        yield Field\TextEditorField::new('description')->setRequired(false)->hideOnIndex();
        yield AssociationField::new('categories')->hideOnIndex();
        yield AssociationField::new('themes')->hideOnIndex();
        yield AssociationField::new('creators')->hideOnIndex();
    }
}
