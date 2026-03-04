<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Entity\ValueObject\Title;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TitleType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'class' => Title::class,
            'label' => 'title.label',
            'empty_data' => '',
        ]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ValueObjectType::class;
    }
}
