<?php

declare(strict_types=1);

namespace App\Cache\SurfSession;

use App\Entity\User;

final class SurfSessionCacheTags
{
    public const string USER_LIST_KEY_PREFIX = 'surf_session.user_list.';

    public static function listForUser(User $user): string
    {
        return sprintf('%s%d', self::USER_LIST_KEY_PREFIX, $user->id);
    }
}
