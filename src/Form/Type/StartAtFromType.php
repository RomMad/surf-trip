<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StartAtFromType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'start_at_from.label',
            'required' => false,
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return DateImmutableType::class;
    }
}
