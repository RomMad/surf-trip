<?php

declare(strict_types=1);

namespace App\Enum\SurfSession;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum SurfSessionDuration: int implements TranslatableInterface
{
    use EnumTrait;

    case Minutes30 = 30;
    case Minutes60 = 60;
    case Minutes90 = 90;
    case Minutes120 = 120;
    case Minutes150 = 150;
    case Minutes180 = 180;
    case Minutes210 = 210;
    case Minutes240 = 240;
    case Minutes270 = 270;
    case Minutes300 = 300;

    private const int MIN = self::Minutes30->value;
    private const int MAX = self::Minutes300->value;

    public static function fromMinutes(int $minutes): self
    {
        if ($duration = self::tryFrom($minutes)) {
            return $duration;
        }

        $clampedMinutes = max(self::MIN, min(self::MAX, $minutes));
        $normalizedMinutes = (int) (round($clampedMinutes / 30) * 30);

        return self::from($normalizedMinutes);
    }

    public function label(): string
    {
        $hours = intdiv($this->value, 60);
        $minutes = $this->value % 60;

        return match (true) {
            0 === $hours => sprintf('%dm', $minutes),
            0 === $minutes => sprintf('%dh', $hours),
            default => sprintf('%dh%02d', $hours, $minutes),
        };
    }
}
