<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class DataTable
{
    /** @var list<string> */
    public array $headers = [];

    /** @var list<array<mixed>> */
    public array $data = [];
}
