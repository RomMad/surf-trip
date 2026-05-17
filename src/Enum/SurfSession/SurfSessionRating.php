<?php

declare(strict_types=1);

namespace App\Enum\SurfSession;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum SurfSessionRating: int implements TranslatableInterface
{
    use EnumTrait;

    case VeryBad = 1;
    case Bad = 2;
    case Average = 3;
    case Good = 4;
    case Excellent = 5;

    public function label(): string
    {
        return match ($this) {
            self::VeryBad => 'very_bad.label',
            self::Bad => 'bad.label',
            self::Average => 'average.label',
            self::Good => 'good.label',
            self::Excellent => 'excellent.label',
        };
    }
}
