<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Symfony\Contracts\Translation\TranslatorInterface;

trait TranslatorTrait
{
    /**
     * @param array<string, string|float|int> $parameters
     */
    protected function trans(string $id, array $parameters = [], ?string $domain = 'messages', ?string $locale = null): string
    {
        $translator = self::getContainer()->get('translator');

        if (!$translator instanceof TranslatorInterface) {
            throw new \Exception(sprintf('The "translator" service is not an instance of %s.', TranslatorInterface::class));
        }

        return $translator->trans($id, $parameters, $domain, $locale);
    }
}
