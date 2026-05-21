<?php

declare(strict_types=1);

namespace App\Form\Trip;

use App\Enum\User\SurfLevel;
use App\Form\Model\Trip\TripSearchInput;
use App\Form\Type\InlineSearchFormType;
use App\Form\Type\PeriodType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TripSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', SearchType::class, [
                'required' => false,
                'label' => 'search.label',
                'attr' => [
                    'placeholder' => 'search.placeholder',
                ],
            ])
            ->add('period', PeriodType::class)
            ->add('requiredLevels', EnumType::class, [
                'class' => SurfLevel::class,
                'label' => 'required_levels.label',
                'attr' => [
                    'placeholder' => 'required_levels.placeholder',
                ],
                'required' => false,
                'multiple' => true,
                'autocomplete' => true,
            ])
            ->add('location', TextType::class, [
                'required' => false,
                'label' => 'location.label',
                'attr' => [
                    'placeholder' => 'location.placeholder',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TripSearchInput::class,
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return InlineSearchFormType::class;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return '';
    }
}
