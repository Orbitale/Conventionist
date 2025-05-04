<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RoleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('allow_delete', false);
        $resolver->setDefault('expanded', true);
        $resolver->setDefault('required', true);
        $resolver->setDefault('choices', [
            'ROLE_ADMIN' => 'ROLE_ADMIN',
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
