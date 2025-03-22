<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ScheduledActivity;
use App\Entity\TimeSlot;
use App\Entity\User;
use App\Repository\ActivityRepository;
use App\Repository\ScheduledActivityRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SubmitActivityToSlotType extends AbstractType
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
        private readonly ScheduledActivityRepository $scheduledActivityRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['user'];
        /** @var TimeSlot $timeSlot */
        $timeSlot = $options['time_slot'];

        if ($user) {
            $builder
                ->add('selectedActivity', EntityType::class, [
                    'class' => Activity::class,
                    'query_builder' => function (ActivityRepository $repository) use ($user) {
                        return $repository->createQueryBuilder('activity')
                            ->leftJoin('activity.creators', 'creator')
                            ->where('creator IN (:user)')
                            ->setParameter('user', $user)
                        ;
                    },
                    'label' => 'event.activity.submit_activity.selected_activity',
                    'required' => false,
                ])
            ;
        }

        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'help' => $user ? null : 'event.activity.register_as_attendee.form.email.help',
                'help_translation_parameters' => [
                    '%login_url%' => $options['login_url'],
                ],
                'disabled' => $user !== null,
                'required' => true,
                'help_html' => true,
            ])
            ->add('newActivity', ActivityType::class, [
                'label' => false,
                'show_equipment' => false,
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (PostSubmitEvent $event) use ($user, $timeSlot): void {
                /** @var ScheduledActivity $scheduledActivity */
                $scheduledActivity = $event->getData();

                if ($scheduledActivity->selectedActivity) {
                    $scheduledActivity->setActivity($scheduledActivity->selectedActivity);
                }
                if ($scheduledActivity->newActivity) {
                    // New activity always takes precedence.
                    $scheduledActivity->setActivity($scheduledActivity->newActivity);
                }

                if (!$user) {
                    $user = $this->userRepository->findOneBy(['email' => $scheduledActivity->email]);
                }
                if ($user) {
                    $existingRegistration = $this->scheduledActivityRepository->hasSimilarForUser($user, $scheduledActivity->getActivity(), $timeSlot);
                    if ($existingRegistration) {
                        $event->getForm()->addError(new FormError($this->translator->trans('event.error.already_submitted_activity')));
                    }
                }
            }, 1)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ScheduledActivity::class,
            'time_slot' => null,
            'user' => null,
            'login_url' => '',
            'validation_groups' => ['submit_activity', 'Default'],
        ]);
        $resolver->setAllowedTypes('time_slot', [TimeSlot::class]);
        $resolver->setAllowedTypes('user', ['null', User::class]);
    }
}
