<?php

namespace App\Grid;

use App\Entity\Event;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;

final class EventGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'admin_event';
    }

    public function getResourceClass(): string
    {
        return Event::class;
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                StringField::create('name')
                    ->setLabel('Name')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('slug')
                    ->setLabel('Slug')
                    ->setSortable(true)
            )
            // ->addField(
            //    TwigField::create('isOnlineEvent', 'path/to/field/template.html.twig')
            //        ->setLabel('IsOnlineEvent')
            // )
            // ->addField(
            //    TwigField::create('allowActivityRegistration', 'path/to/field/template.html.twig')
            //        ->setLabel('AllowActivityRegistration')
            // )
            // ->addField(
            //    TwigField::create('allowAttendeeRegistration', 'path/to/field/template.html.twig')
            //        ->setLabel('AllowAttendeeRegistration')
            // )
            ->addField(
                StringField::create('locale')
                    ->setLabel('Locale')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('url')
                    ->setLabel('Url')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('description')
                    ->setLabel('Description')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('contactName')
                    ->setLabel('ContactName')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('contactEmail')
                    ->setLabel('ContactEmail')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('contactPhone')
                    ->setLabel('ContactPhone')
                    ->setSortable(true)
            )
            ->addField(
                DateTimeField::create('startsAt')
                    ->setLabel('StartsAt')
            )
            ->addField(
                DateTimeField::create('endsAt')
                    ->setLabel('EndsAt')
            )
            // ->addField(
            //    TwigField::create('published', 'path/to/field/template.html.twig')
            //        ->setLabel('Published')
            // )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('CreatedAt')
            )
            ->addField(
                DateTimeField::create('updatedAt')
                    ->setLabel('UpdatedAt')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
        ;
    }
}
