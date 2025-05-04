<?php

namespace App\Form\Type;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActivityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('maxNumberOfParticipants', NumberType::class, [
                'required' => false,
                'html5' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
        ;
        if ($options['show_equipment']) {
            $builder
                ->add('neededEquipment', AssociativeArrayType::class, [
                    'required' => false,
                    'help' => 'activity.form.equipment.help',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
            'show_equipment' => true,
        ]);
    }
}
