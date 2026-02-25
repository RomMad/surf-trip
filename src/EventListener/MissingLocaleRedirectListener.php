<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 40)]
final class MissingLocaleRedirectListener
{
    private const string DEFAULT_LOCALE = self::LANG_FR;

    private const string LANG_FR = 'fr';
    private const string LANG_EN = 'en';

    private const array SUPPORTED_LOCALES = [
        self::LANG_FR,
        self::LANG_EN,
    ];

    private const array EXCLUDED_PREFIXES = [
        '/api',
        '/docs',
        '/build',
        '/bundles',
        '/_wdt',
        '/_profiler',
    ];

    private const string ASSETS_REGEX = '#\.(?:css|js|map|png|jpe?g|gif|svg|ico|webp|avif|woff2?|ttf|eot|json|txt|xml)$#i';

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->supportsMethod($request)) {
            return;
        }

        $path = $request->getPathInfo();

        if ($this->isExcludedPath($path)) {
            return;
        }

        if ($this->isLocalizedPath($path)) {
            $request->setLocale($this->extractLocale($path));

            return;
        }

        $locale = $request->getPreferredLanguage(self::SUPPORTED_LOCALES) ?? self::DEFAULT_LOCALE;
        $targetPath = $this->buildLocalizedPath($locale, $path, $request);

        $event->setResponse(
            new RedirectResponse($targetPath, Response::HTTP_MOVED_PERMANENTLY)
        );
    }

    private function supportsMethod(Request $request): bool
    {
        return $request->isMethod(Request::METHOD_GET)
            || $request->isMethod(Request::METHOD_HEAD);
    }

    private function isLocalizedPath(string $path): bool
    {
        foreach (self::SUPPORTED_LOCALES as $locale) {
            if ($path === "/{$locale}" || str_starts_with($path, "/{$locale}/")) {
                return true;
            }
        }

        return false;
    }

    private function extractLocale(string $path): string
    {
        return substr($path, 1, 2);
    }

    private function isExcludedPath(string $path): bool
    {
        foreach (self::EXCLUDED_PREFIXES as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return 1 === preg_match(self::ASSETS_REGEX, $path);
    }

    private function buildLocalizedPath(string $locale, string $path, Request $request): string
    {
        $localizedPath = '/'.$locale.('/' === $path ? '' : $path);

        if (null !== $request->getQueryString()) {
            $localizedPath .= '?'.$request->getQueryString();
        }

        return $localizedPath;
    }
}
