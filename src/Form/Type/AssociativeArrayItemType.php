<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AssociativeArrayItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('item_key', $options['key_type'], $options['key_options'])
            ->add('item_value', $options['value_type'], $options['value_options'])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$view->vars['label'] && isset($view->vars['value']['item_key'])) {
            $view->vars['label'] = $view->vars['value']['item_key'];
        }
    }

    public function getBlockPrefix(): string
    {
        return 'associative_array_item';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'key_type' => TextType::class,
            'value_type' => TextType::class,
            'key_options' => [],
            'value_options' => [],
        ]);
    }
}
