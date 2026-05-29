<?php

declare(strict_types=1);

namespace App\Form\SurfSession;

use App\Entity\User;
use App\Enum\SurfSession\SurfSessionRating;
use App\Form\Model\SurfSession\SurfSessionWriteModel;
use App\Form\Type\Autocomplete\TripAutocompleteType;
use App\Form\Type\DateTimeImmutableType;
use App\ReadModel\Trip\TripSelectReadModel;
use App\Repository\TripRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class SurfSessionFormType extends AbstractType
{
    public function __construct(
        private readonly TripRepository $tripRepository,
        private readonly TokenStorageInterface $tokenStorage,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var SurfSessionWriteModel $surfSession */
        $surfSession = $builder->getData();

        $builder
            ->add('spot', TextType::class, [
                'label' => 'surf_session.spot.label',
                'attr' => [
                    'placeholder' => 'surf_session.spot.placeholder',
                    'maxlength' => 100,
                ],
            ])
            ->add('board', TextType::class, [
                'label' => 'surf_session.board.label',
                'attr' => [
                    'placeholder' => 'surf_session.board.placeholder',
                    'maxlength' => 100,
                ],
                'required' => false,
            ])
            ->add('startAt', DateTimeImmutableType::class, [
                'label' => 'start_at.label',
            ])
            ->add('endAt', DateTimeImmutableType::class, [
                'label' => 'end_at.label',
            ])
            ->add('rating', EnumType::class, [
                'class' => SurfSessionRating::class,
                'label' => 'surf_session.rating.label',
                'placeholder' => 'surf_session.rating.placeholder',
                'expanded' => true,
                'required' => false,
            ])
            ->add('objective', TextareaType::class, [
                'label' => 'surf_session.objective.label',
                'attr' => [
                    'placeholder' => 'surf_session.objective.placeholder',
                    'maxlength' => 1000,
                    'rows' => 4,
                ],
                'required' => false,
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'surf_session.comment.label',
                'attr' => [
                    'placeholder' => 'surf_session.comment.placeholder',
                    'maxlength' => 5000,
                    'rows' => 6,
                ],
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($surfSession): void {
                $referenceAt = $surfSession->startAt ?? new \DateTimeImmutable();
                $trip = $surfSession->trip;

                if (!$trip) {
                    $currentUser = $this->getCurrentUser();
                    $trip = $this->tripRepository->findSuggestedTripByDate($currentUser, $referenceAt);
                    $surfSession->trip = $trip;
                }

                $choices = $trip ? [$trip] : [];

                $this->addTripField($event->getForm(), $choices, [
                    'reference_at' => $referenceAt->format(\DateTimeInterface::ATOM),
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($surfSession): void {
                $submittedTripId = $event->getData()['trip'] ?? null;

                if (!$submittedTripId || (int) $submittedTripId === $surfSession->trip?->id) {
                    return;
                }

                $currentUser = $this->getCurrentUser();
                $choices = $this->tripRepository->findSelectReadModelsByUserAndTripId($currentUser, (int) $submittedTripId);

                $this->addTripField($event->getForm(), $choices);
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SurfSessionWriteModel::class,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'surf_session';
    }

    /**
     * @param array<int, TripSelectReadModel> $choices
     * @param array<string, string>           $extraOptions
     */
    private function addTripField(FormInterface $form, array $choices = [], array $extraOptions = []): void
    {
        $form
            ->add('trip', TripAutocompleteType::class, [
                'choices' => $choices,
                'extra_options' => $extraOptions,
            ])
        ;
    }

    private function getCurrentUser(): User
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('The user must be logged in to create or edit a surf session.');
        }

        return $user;
    }
}
