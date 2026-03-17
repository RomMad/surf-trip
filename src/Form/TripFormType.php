<?php

declare(strict_types=1);

namespace App\Form;

use App\Enum\Trip\RequiredLevel;
use App\Form\Model\TripWriteModel;
use App\Form\Type\LocationType;
use App\Form\Type\TitleType;
use App\ReadModel\Trip\TripOwnerReadModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TripFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $trip = $builder->getData();

        $builder
            ->add('title', TitleType::class, [
            ])
            ->add('location', LocationType::class, [
            ])
            ->add('startAt', DateTimeType::class, [
                'label' => 'start_at.label',
                'widget' => 'single_text',
            ])
            ->add('endAt', DateTimeType::class, [
                'label' => 'end_at.label',
                'widget' => 'single_text',
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
        ;

        if ($trip instanceof TripWriteModel && [] !== $trip->owners) {
            $builder->add('owners', ChoiceType::class, [
                'choices' => $trip->owners,
                'choice_label' => fn (TripOwnerReadModel $owner): string => $owner->fullName,
                'choice_value' => fn (TripOwnerReadModel $owner): int => $owner->id,
                'choice_translation_domain' => false,
                'label' => 'owners.label',
                'multiple' => true,
                'autocomplete' => true,
            ]);
        }
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
