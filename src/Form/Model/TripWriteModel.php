<?php

declare(strict_types=1);

namespace App\Form\Model;

use App\Entity\Trip;
use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use App\ReadModel\Trip\TripOwnerReadModel;
use App\ReadModel\Trip\TripShowReadModel;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\ObjectMapper\Condition\TargetClass;

#[Map(source: TripShowReadModel::class)]
#[Map(target: Trip::class)]
final class TripWriteModel
{
    public ?Title $title = null;

    public ?Location $location = null;

    public ?\DateTimeImmutable $startAt = null;

    public ?\DateTimeImmutable $endAt = null;

    /** @var RequiredLevel[] */
    public array $requiredLevels = [];

    public ?string $description = null;

    /** @var list<TripOwnerReadModel> */
    #[Map(if: new TargetClass(self::class))]
    public array $owners = [];
}
