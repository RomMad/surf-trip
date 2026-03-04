<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\DataTransformer\ValueObjectToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ValueObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new ValueObjectToStringTransformer($options['class']));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('class')
            ->setAllowedTypes('class', 'string')
        ;
    }

    #[\Override]
    public function getParent(): string
    {
        return TextType::class;
    }
}
