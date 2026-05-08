<?php

declare(strict_types=1);

namespace App\Enum\User;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum SurfLevel: int implements TranslatableInterface
{
    use EnumTrait;

    case Beginner = 1;
    case Intermediate = 2;
    case Advanced = 3;
    case Expert = 4;

    public function label(): string
    {
        return match ($this) {
            self::Beginner => 'beginner.label',
            self::Intermediate => 'intermediate.label',
            self::Advanced => 'advanced.label',
            self::Expert => 'expert.label',
        };
    }
}
