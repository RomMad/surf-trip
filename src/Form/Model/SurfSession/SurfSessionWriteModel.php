<?php

declare(strict_types=1);

namespace App\Form\Model\SurfSession;

use App\Entity\SurfSession;
use App\Enum\SurfSession\SurfSessionRating;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(source: SurfSession::class)]
#[Map(target: SurfSession::class)]
final class SurfSessionWriteModel
{
    #[Assert\Length(max: 100, maxMessage: 'surf_session.board.max_length')]
    public ?string $board = null;

    #[Assert\NotBlank(message: 'surf_session.spot.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'surf_session.spot.max_length')]
    public ?string $spot = null;

    #[Assert\NotNull(message: 'surf_session.start_at.not_null')]
    public ?\DateTimeImmutable $startAt = null;

    #[Assert\Sequentially([
        new Assert\NotNull(message: 'surf_session.end_at.not_null'),
        new Assert\Expression(
            expression: 'value > this.startAt',
            message: 'surf_session.end_at.after_start_at'
        ),
    ])]
    public ?\DateTimeImmutable $endAt = null;

    public ?SurfSessionRating $rating = null;

    #[Assert\Length(max: 1000, maxMessage: 'surf_session.objective.max_length')]
    public ?string $objective = null;

    #[Assert\Length(max: 5000, maxMessage: 'surf_session.comment.max_length')]
    public ?string $comment = null;
}
