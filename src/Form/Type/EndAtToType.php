<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EndAtToType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'end_at_to.label',
            'required' => false,
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return DateImmutableType::class;
    }
}
