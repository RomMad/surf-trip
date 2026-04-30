<?php

declare(strict_types=1);

namespace App\Enum\User;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum Locale: string implements TranslatableInterface
{
    use EnumTrait;

    case French = 'fr';
    case English = 'en';

    public const self DEFAULT = self::French;

    /** @var list<string> */
    public const array SUPPORTED_LOCALES = [
        self::French->value,
        self::English->value,
    ];
}
