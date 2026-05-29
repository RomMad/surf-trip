<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Calendar\CalendarLinkableInterface;
use App\Entity\Traits\SurfSessionCalendarLinkableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Enum\SurfSession\SurfSessionRating;
use App\Repository\SurfSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SurfSessionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(name: 'idx_surf_session_start_at', fields: ['startAt'])]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['surf_session:read']],
            security: 'is_granted("VIEW", object)',
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['surf_session:read']],
            security: 'is_granted("ROLE_USER")',
        ),
        new Post(
            normalizationContext: ['groups' => ['surf_session:read']],
            denormalizationContext: ['groups' => ['surf_session:write']],
            security: 'is_granted("EDIT", object)',
        ),
        new Patch(
            normalizationContext: ['groups' => ['surf_session:read']],
            denormalizationContext: ['groups' => ['surf_session:write']],
            security: 'is_granted("EDIT", object)',
        ),
        new Delete(
            security: 'is_granted("DELETE", object)',
        ),
    ],
    normalizationContext: ['groups' => ['surf_session:read']],
    denormalizationContext: ['groups' => ['surf_session:write']],
    order: ['startAt' => 'DESC'],
    paginationEnabled: true,
)]
final class SurfSession implements CalendarLinkableInterface
{
    use TimestampableTrait;
    use SurfSessionCalendarLinkableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['surf_session:read'])]
    public private(set) ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100, maxMessage: 'surf_session.board.max_length')]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?string $board = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'surf_session.spot.not_blank')]
    #[Assert\Length(max: 100, maxMessage: 'surf_session.spot.max_length')]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?string $spot = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'surf_session.start_at.not_null')]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Assert\Sequentially([
        new Assert\NotNull(message: 'surf_session.end_at.not_null'),
        new Assert\Expression(
            expression: 'value > this.startAt',
            message: 'surf_session.end_at.after_start_at'
        ),
    ])]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?\DateTimeImmutable $endAt = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true, enumType: SurfSessionRating::class)]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?SurfSessionRating $rating = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 1000, maxMessage: 'surf_session.objective.max_length')]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?string $objective = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000, maxMessage: 'surf_session.comment.max_length')]
    #[Groups(['surf_session:read', 'surf_session:write'])]
    public ?string $comment = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull()]
    #[Groups(['surf_session:read'])]
    public ?User $user = null;
}
