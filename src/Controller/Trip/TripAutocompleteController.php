<?php

declare(strict_types=1);

namespace App\Controller\Trip;

use App\Controller\AbstractAutocompleteController;
use App\Entity\User;
use App\Enum\User\UserRole;
use App\Repository\TripRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class TripAutocompleteController extends AbstractAutocompleteController
{
    public function __construct(
        private readonly TripRepository $tripRepository,
    ) {}

    #[Route(
        path: '/autocomplete/trips/search',
        methods: [Request::METHOD_GET],
    )]
    #[IsGranted(UserRole::USER)]
    public function __invoke(
        Request $request,
        #[CurrentUser()]
        User $currentUser,
        #[MapQueryParameter()]
        string $query = '',
    ): JsonResponse {
        $referenceAt = $this->resolveReferenceAt($request);
        $results = $this->tripRepository->findTripChoicesByQuery($query, $referenceAt, $currentUser);

        return $this->json([
            'results' => $results,
        ]);
    }

    private function resolveReferenceAt(Request $request): \DateTimeImmutable
    {
        $extraOptions = $this->getExtraOptions($request);
        $referenceAt = $extraOptions['reference_at'] ?? null;

        if (null === $referenceAt) {
            return new \DateTimeImmutable();
        }

        return \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $referenceAt) ?: new \DateTimeImmutable();
    }
}
