<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('allow_delete', false);
        $resolver->setDefault('choices', [
            'admin' => 'ROLE_ADMIN',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
