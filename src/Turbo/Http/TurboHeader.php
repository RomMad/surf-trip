<?php

declare(strict_types=1);

namespace App\Turbo\Http;

final class TurboHeader
{
    public const string FRAME = 'Turbo-Frame';
    public const string FRAME_SERVER = 'HTTP_TURBO_FRAME';
}
