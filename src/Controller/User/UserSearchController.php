<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Enum\User\UserRole;
use App\ReadModel\Trip\TripOwnerReadModel;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserSearchController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    #[Route(
        path: '/autocomplete/users/search',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(#[MapQueryParameter()] string $query = ''): JsonResponse
    {
        $results = array_map(
            static fn (TripOwnerReadModel $owner): array => [
                'value' => $owner->id,
                'text' => $owner->fullName,
            ],
            $this->userRepository->findOwnerReadModelsByQuery($query)
        );

        return $this->json([
            'results' => $results,
        ]);
    }
}
