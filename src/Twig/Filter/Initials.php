<?php

declare(strict_types=1);

namespace App\Twig\Filter;

use Twig\Attribute\AsTwigFilter;

final readonly class Initials
{
    #[AsTwigFilter('initials')]
    public function initials(string $value): string
    {
        $parts = preg_split('/\s+/', trim($value));

        if (1 === count($parts)) {
            return mb_substr($parts[0], 0, 1)
                |> mb_strtoupper(...);
        }

        return
            (mb_substr($parts[0], 0, 1)
            .mb_substr(array_last($parts), 0, 1))
                |> mb_strtoupper(...);
    }
}
