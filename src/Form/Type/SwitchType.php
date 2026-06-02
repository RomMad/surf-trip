<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

final class SwitchType extends AbstractType
{
    #[\Override]
    public function getParent(): string
    {
        return CheckboxType::class;
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'switch';
    }
}
