<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ValueObject\Username;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UsernameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Username::class,
            'label' => 'username.label',
            'empty_data' => '',
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ValueObjectType::class;
    }
}
