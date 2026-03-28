<?php

namespace App\Controller\Admin\Crud;

use App\Controller\Admin\Traits\GenericCrudMethods;
use App\Entity\Attendee;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

/**
 * @extends AbstractCrudController<Attendee>
 */
final class AttendeeCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Attendee::class;
    }
}
