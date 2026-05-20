<?php

declare(strict_types=1);

namespace App\Form\Model\SurfSession;

final class SurfSessionSearchInput
{
    public ?string $query = null;

    public ?\DateTimeImmutable $startAtFrom = null;

    public ?\DateTimeImmutable $endAtTo = null;
}
