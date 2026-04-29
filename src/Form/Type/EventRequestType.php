<?php

namespace App\Form\Type;

use App\Entity\EventRequest;
use App\Enum\EventRequestType as EventRequestTypeEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EventRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('requestType', EnumType::class, [
                'class' => EventRequestTypeEnum::class,
                'choice_label' => fn (EventRequestTypeEnum $t) => $t->getLabel(),
                'label' => 'event.request.form.type',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'event.request.form.message',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventRequest::class,
        ]);
    }
}
