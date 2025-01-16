<?php

namespace App\Controller\Admin\Traits;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

trait DisableAllActions
{
    public function configureActions(Actions $actions): Actions
    {
        return $actions->disable(
            Action::BATCH_DELETE,
            Action::DELETE,
            Action::DETAIL,
            Action::EDIT,
            Action::INDEX,
            Action::NEW,
            Action::SAVE_AND_ADD_ANOTHER,
            Action::SAVE_AND_CONTINUE,
            Action::SAVE_AND_RETURN,
        );
    }
}
