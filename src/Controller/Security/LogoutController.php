<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController extends AbstractController
{
    public const string ROUTE = 'app.logout';

    #[Route(path: '/logout', name: self::ROUTE, methods: [Request::METHOD_GET])]
    public function __invoke(): void {}
}
