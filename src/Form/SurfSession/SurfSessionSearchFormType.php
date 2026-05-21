<?php

declare(strict_types=1);

namespace App\Form\SurfSession;

use App\Form\Model\SurfSession\SurfSessionSearchInput;
use App\Form\Type\PeriodType;
use App\Form\Type\QueryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SurfSessionSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('query', QueryType::class)
            ->add('period', PeriodType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SurfSessionSearchInput::class,
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
            'allow_extra_fields' => true,
        ]);
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return '';
    }
}
