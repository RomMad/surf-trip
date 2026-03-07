<?php

declare(strict_types=1);

namespace App\Tests\Fixtures;

use Zenstruck\Foundry\Story;

final class DefaultStory extends Story
{
    public function build(): void
    {
        UserStory::load();
        TripStory::load();
    }
}
