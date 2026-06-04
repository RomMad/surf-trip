<?php

declare(strict_types=1);

namespace App\Form\Model\SurfSession;

use App\Entity\SurfSession;
use App\Enum\SurfSession\SurfSessionDuration;
use App\Enum\SurfSession\SurfSessionRating;
use App\ObjectMapper\SurfSessionEndAtToDurationTransformer;
use App\ObjectMapper\SurfSessionStartAtToDateTransformer;
use App\ObjectMapper\SurfSessionStartAtToTimeTransformer;
use App\ObjectMapper\SurfSessionWriteModelToEndAtTransformer;
use App\ObjectMapper\SurfSessionWriteModelToStartAtTransformer;
use App\ObjectMapper\TripSelectReadModelToTripTransformer;
use App\ReadModel\Trip\TripSelectReadModel;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(target: SurfSession::class)]
final class SurfSessionWriteModel
{
    #[Assert\Length(max: 100, maxMessage: 'surf_session.board.max_length')]
    public ?string $board = null;

    #[Assert\NotBlank(message: 'surf_session.spot.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'surf_session.spot.max_length')]
    public ?string $spot = null;

    #[Assert\NotNull(message: 'surf_session.date.not_null')]
    #[Map(target: 'startAt', transform: SurfSessionWriteModelToStartAtTransformer::class)]
    #[Map(source: 'startAt', transform: SurfSessionStartAtToDateTransformer::class)]
    public ?\DateTimeImmutable $date = null;

    #[Assert\NotBlank(message: 'surf_session.start_time.not_blank')]
    #[Assert\Time(message: 'surf_session.start_time.invalid_format', withSeconds: false)]
    #[Map(source: 'startAt', transform: SurfSessionStartAtToTimeTransformer::class)]
    public ?string $startTime = null;

    #[Assert\NotNull(message: 'surf_session.duration_minutes.not_null')]
    #[Map(target: 'endAt', transform: SurfSessionWriteModelToEndAtTransformer::class)]
    #[Map(source: 'endAt', transform: SurfSessionEndAtToDurationTransformer::class)]
    public ?SurfSessionDuration $durationMinutes = null;

    public ?SurfSessionRating $rating = null;

    #[Assert\Length(max: 1000, maxMessage: 'surf_session.objective.max_length')]
    public ?string $objective = null;

    #[Assert\Length(max: 5000, maxMessage: 'surf_session.comment.max_length')]
    public ?string $comment = null;

    #[Map(transform: TripSelectReadModelToTripTransformer::class)]
    public ?TripSelectReadModel $trip = null;

    public function getStartAt(): ?\DateTimeImmutable
    {
        if (null === $this->date || null === $this->startTime) {
            return null;
        }

        return \DateTimeImmutable::createFromFormat(
            'Y-m-d H:i',
            sprintf('%s %s', $this->date->format('Y-m-d'), $this->startTime),
        );
    }
}
