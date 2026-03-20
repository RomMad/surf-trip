<?php

declare(strict_types=1);

namespace App\Form;

use App\Enum\Trip\RequiredLevel;
use App\Form\Model\TripWriteModel;
use App\Form\Type\DateTimeImmutableType;
use App\Form\Type\LocationType;
use App\Form\Type\OwnersAutocompleteType;
use App\Form\Type\TitleType;
use App\ReadModel\Trip\TripOwnerReadModel;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFormType extends AbstractType
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trip = $builder->getData();

        $builder
            ->add('title', TitleType::class)
            ->add('location', LocationType::class)
            ->add('startAt', DateTimeImmutableType::class, [
                'label' => 'start_at.label',
            ])
            ->add('endAt', DateTimeImmutableType::class, [
                'label' => 'end_at.label',
            ])
            ->add('requiredLevels', EnumType::class, [
                'class' => RequiredLevel::class,
                'label' => 'required_levels.label',
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description.label',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('owners', OwnersAutocompleteType::class, [
                'choices' => $trip->owners,
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($trip): void {
                $ownerIds = array_map(
                    fn (string $ownerId) => (int) $ownerId,
                    $event->getData()['owners'] ?? [],
                );

                if ($this->ownersUnchanged($ownerIds, $trip->owners)) {
                    return;
                }

                $event->getForm()
                    ->add('owners', OwnersAutocompleteType::class, [
                        'choices' => $this->userRepository->findOwnerReadModelsByIds($ownerIds),
                    ])
                ;
            })
        ;
    }

    /**
     * @param int[]                    $newOwnerIds
     * @param list<TripOwnerReadModel> $currentOwners
     */
    private function ownersUnchanged(array $newOwnerIds, array $currentOwners): bool
    {
        $currentOwnerIds = array_map(
            fn (TripOwnerReadModel $owner) => $owner->id,
            $currentOwners
        );

        return $newOwnerIds === $currentOwnerIds;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripWriteModel::class,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'trip';
    }
}
