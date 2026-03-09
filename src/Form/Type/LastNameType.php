<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ValueObject\LastName;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LastNameType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => LastName::class,
            'label' => 'last_name.label',
            'empty_data' => '',
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ValueObjectType::class;
    }
}
