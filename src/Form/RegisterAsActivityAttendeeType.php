<?php

namespace App\Form;

use App\Entity\Attendee;
use App\Repository\AttendeeRepository;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class RegisterAsActivityAttendeeType extends AbstractType
{
    public function __construct(
        private readonly AttendeeRepository $attendeeRepository,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'help' => $options['enable_email'] ? 'event.activity.register_as_attendee.form.email.help' : null,
                'help_translation_parameters' => [
                    '%login_url%' => $options['login_url'],
                ],
                'disabled' => !$options['enable_email'],
                'required' => true,
                'help_html' => true,
            ])
            ->add('name', TextType::class, [
                'label' => 'event.activity.register_as_attendee.form.name',
                'required' => true,
            ])
            ->add('numberOfAttendees', NumberType::class, [
                'label' => 'event.activity.register_as_attendee.form.numberOfAttendees',
                'html5' => true,
                'required' => true,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event): void {
                /** @var Attendee $attendee */
                $attendee = $event->getData();
                $user = $this->userRepository->findOneBy(['email' => $attendee->email]);
                if (!$user) {
                    // No user = will create a user automatically in the controller
                    return;
                }
                $existingRegistration = $this->attendeeRepository->findOneBy([
                    'registeredBy' => $user,
                    'scheduledActivity' => $attendee->getScheduledActivity(),
                ]);
                if ($existingRegistration) {
                    $event->getForm()->addError(new FormError($this->translator->trans('event.error.already_registered_to_activity')));
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Attendee::class,
            'enable_email' => true,
            'login_url' => '',
        ]);
    }
}
