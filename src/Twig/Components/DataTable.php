<?php

declare(strict_types=1);

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
final class DataTable
{
    /** @var list<string> */
    public array $headers = [];

    /** @var list<array<mixed>> */
    public array $data = [];

    #[ExposeInTemplate('empty_message')]
    public string $emptyMessage = 'no_records_found.label';
}
