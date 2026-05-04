<?php

declare(strict_types=1);

namespace App\Enum\User;

enum Locale: string
{
    case French = 'fr';
    case English = 'en';

    public const self DEFAULT = self::French;

    /** @var list<string> */
    public const array SUPPORTED_LOCALES = [
        self::French->value,
        self::English->value,
    ];
}
