<?php

declare(strict_types=1);

namespace App\Enum;

use Symfony\Contracts\Translation\TranslatableInterface;

enum RequiredLevel: int implements TranslatableInterface
{
    use EnumTrait;

    case BEGINNER = 1;
    case INTERMEDIATE = 2;
    case ADVANCED = 3;

    public function label(): string
    {
        return match ($this) {
            self::BEGINNER => 'beginner.label',
            self::INTERMEDIATE => 'intermediate.label',
            self::ADVANCED => 'advanced.label',
        };
    }
}
