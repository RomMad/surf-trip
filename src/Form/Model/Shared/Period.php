<?php

declare(strict_types=1);

namespace App\Form\Model\Shared;

class Period
{
    public ?\DateTimeImmutable $from = null;

    public ?\DateTimeImmutable $to = null;
}
