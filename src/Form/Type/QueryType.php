<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class QueryType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'search.label',
            'attr' => [
                'placeholder' => 'search.placeholder',
            ],
            'required' => false,
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return SearchType::class;
    }
}
