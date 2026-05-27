<?php

declare(strict_types=1);

namespace App\Cache\Dashboard;

use App\Entity\User;

final class DashboardCacheTags
{
    public const string USER_STATS_KEY_PREFIX = 'dashboard.user_stats.';

    public static function statsForUser(User $user): string
    {
        return sprintf('%s%d', self::USER_STATS_KEY_PREFIX, $user->id);
    }
}
