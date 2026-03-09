<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ValueObject\Email;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EmailType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Email::class,
            'label' => 'email.label',
            'empty_data' => '',
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ValueObjectType::class;
    }
}
