<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssociativeArrayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $e) {
            $output = [];
            foreach ($e->getData() ?: [] as $key => $value) {
                $output[] = [
                    'item_key' => $key,
                    'item_value' => $value,
                ];
            }

            $e->setData($output);
        }, 1);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $e) {
            $input = [];
            foreach ($e->getData() ?: [] as $data) {
                $input[$data['item_key']] = $data['item_value'];
            }

            $e->setData($input);
        }, 1);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'associative_array';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => AssociativeArrayItemType::class,
        ]);
    }
}
