<?php

declare(strict_types=1);

namespace App\Turbo\Http;

use Symfony\UX\Turbo\TurboBundle;

final class TurboContentType
{
    public const string STREAM_MEDIA_TYPE = TurboBundle::STREAM_MEDIA_TYPE;
    public const string STREAM_HTML_UTF8 = self::STREAM_MEDIA_TYPE.'; charset=UTF-8';
}
