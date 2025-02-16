<?php

namespace App\Controller\Admin;

use App\Entity\Attendee;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

final class AttendeeCrudController extends AbstractCrudController
{
    use GenericCrudMethods;

    public static function getEntityFqcn(): string
    {
        return Attendee::class;
    }
}
