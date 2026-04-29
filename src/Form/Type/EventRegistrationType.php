<?php

namespace App\Form\Type;

use App\Entity\EventRegistration;
use App\Enum\EventRegistrationStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EventRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', EnumType::class, [
                'class' => EventRegistrationStatus::class,
                'choice_label' => fn (EventRegistrationStatus $s) => $s->value,
                'label' => 'event.registration.form.status',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'event.registration.form.notes',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventRegistration::class,
        ]);
    }
}
