<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\Shared\Period;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PeriodType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('from', DateImmutableType::class, [
                'label' => 'period.from.label',
                'required' => false,
            ])
            ->add('to', DateImmutableType::class, [
                'label' => 'period.to.label',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Period::class,
        ]);
    }
}
