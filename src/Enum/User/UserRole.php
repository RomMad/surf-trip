<?php

declare(strict_types=1);

namespace App\Enum\User;

use App\Enum\EnumTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

enum UserRole: string implements TranslatableInterface
{
    use EnumTrait;

    case User = 'ROLE_USER';
    case Admin = 'ROLE_ADMIN';

    public const USER = self::User->value;
    public const ADMIN = self::Admin->value;

    public function label(): string
    {
        return match ($this) {
            self::User => 'role.user.label',
            self::Admin => 'role.admin.label',
        };
    }
}
