<?php

declare(strict_types=1);

namespace App\Form\Model\Trip;

use App\Entity\Trip;
use App\Entity\ValueObject\Title;
use App\Enum\User\SurfLevel;
use App\Form\Model\Shared\LocationInput;
use App\ObjectMapper\Location\LocationInputToLocationTransformer;
use App\ObjectMapper\Trip\OwnerReadModelToUserTransformer;
use App\ReadModel\Trip\TripOwnerReadModel;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: Trip::class)]
final class TripWriteModel
{
    public ?Title $title = null;

    #[Assert\Valid]
    #[Map(transform: LocationInputToLocationTransformer::class)]
    public LocationInput $location;

    #[Assert\NotNull(message: 'trip.start_at.not_null')]
    public ?\DateTimeImmutable $startAt = null;

    #[Assert\Sequentially([
        new Assert\NotNull(message: 'trip.end_at.not_null'),
        new Assert\Expression(
            expression: 'value >= this.startAt',
            message: 'trip.end_at.before_start_at'
        ),
    ])]
    public ?\DateTimeImmutable $endAt = null;

    /** @var SurfLevel[] */
    #[Assert\Count(min: 1, minMessage: 'trip.required_levels.min_count')]
    #[Assert\All([
        new Assert\Type(type: SurfLevel::class, message: 'trip.required_levels.invalid_type'),
    ])]
    public array $requiredLevels = [];

    #[Assert\Length(max: 5000, maxMessage: 'trip.description.max_length')]
    public ?string $description = null;

    /** @var list<TripOwnerReadModel> */
    #[Assert\Count(min: 1, minMessage: 'trip.owner.min_count')]
    #[Map(transform: OwnerReadModelToUserTransformer::class)]
    public array $owners = [];

    public function __construct()
    {
        $this->location = new LocationInput();
    }
}
