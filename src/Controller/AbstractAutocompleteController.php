<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AbstractAutocompleteController extends AbstractController
{
    protected const string EXTRA_OPTIONS = 'extra_options';

    /**
     * @return array<string, scalar>
     */
    protected function getExtraOptions(Request $request): array
    {
        if (!$request->query->has(self::EXTRA_OPTIONS)) {
            return [];
        }

        try {
            return $this->getDecodedExtraOptions($request->query->getString(self::EXTRA_OPTIONS));
        } catch (\JsonException $jsonException) {
            throw new BadRequestHttpException('The extra options cannot be parsed.', $jsonException);
        }
    }

    /**
     * @return array<string, scalar>
     */
    private function getDecodedExtraOptions(string $extraOptions): array
    {
        return json_decode(base64_decode($extraOptions, true), true, flags: \JSON_THROW_ON_ERROR);
    }
}
