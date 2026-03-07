<?php

declare(strict_types=1);

namespace App\Enum\Trip;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum RequiredLevel: int implements TranslatableInterface
{
    use EnumTrait;

    case Beginner = 1;
    case Intermediate = 2;
    case Advanced = 3;

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'beginner.label',
            self::Intermediate => 'intermediate.label',
            self::Advanced => 'advanced.label',
        };
    }
}
