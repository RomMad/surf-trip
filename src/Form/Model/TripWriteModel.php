<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Trip;
use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use App\ObjectMapper\OwnerReadModelToUserTransformer;
use App\ReadModel\Trip\TripOwnerReadModel;
use App\ReadModel\Trip\TripShowReadModel;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Condition\TargetClass;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(source: TripShowReadModel::class)]
#[Map(target: Trip::class)]
final class TripWriteModel
{
    public ?Title $title = null;

    public ?Location $location = null;

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

    /** @var RequiredLevel[] */
    #[Assert\Count(min: 1, minMessage: 'trip.required_levels.min_count')]
    #[Assert\All([
        new Assert\Type(type: RequiredLevel::class, message: 'trip.required_levels.invalid_type'),
    ])]
    public array $requiredLevels = [];

    #[Assert\Length(max: 5000, maxMessage: 'trip.description.max_length')]
    public ?string $description = null;

    /** @var list<TripOwnerReadModel> */
    #[Assert\Count(min: 1, minMessage: 'trip.owner.min_count')]
    #[Map(if: new TargetClass(self::class))]
    #[Map(if: new TargetClass(Trip::class), transform: OwnerReadModelToUserTransformer::class)]
    public array $owners = [];
}
