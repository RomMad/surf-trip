<?php

declare(strict_types=1);

namespace App\Form;

use App\Enum\RequiredLevel;
use App\Form\Model\TripFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TripFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search', SearchType::class, [
                'required' => false,
                'label' => 'search.label',
                'attr' => [
                    'placeholder' => 'search.placeholder',
                ],
            ])
            ->add('requiredLevels', EnumType::class, [
                'class' => RequiredLevel::class,
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
            'data_class' => TripFilter::class,
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return '';
    }
}
