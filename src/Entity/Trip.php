<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Enum\Trip\RequiredLevel;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    normalizationContext: ['groups' => ['trip:read']],
    denormalizationContext: ['groups' => ['trip:write']],
)]
#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'trip.title.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'trip.title.max_length')]
    #[Groups(['trip:read', 'trip:write'])]
    private string $title = '';

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'trip.location.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'trip.location.max_length')]
    #[Groups(['trip:read', 'trip:write'])]
    private string $location = '';

    #[ORM\Column]
    #[Assert\NotNull(message: 'trip.start_at.not_null')]
    #[Groups(['trip:read', 'trip:write'])]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Assert\Sequentially([
        new Assert\NotNull(message: 'trip.end_at.not_null'),
        new Assert\Expression(
            expression: 'value >= this.getStartAt()',
            message: 'trip.end_at.before_start_at'
        ),
    ])]
    #[Groups(['trip:read', 'trip:write'])]
    private ?\DateTimeImmutable $endAt = null;

    /** @var RequiredLevel[] */
    #[ORM\Column(type: Types::JSONB, enumType: RequiredLevel::class)]
    #[Assert\Count(min: 1, minMessage: 'trip.required_levels.min_count')]
    #[Assert\All([
        new Assert\Type(type: RequiredLevel::class, message: 'trip.required_levels.invalid_type'),
    ])]
    #[Groups(['trip:read', 'trip:write'])]
    private array $requiredLevels = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000, maxMessage: 'trip.description.max_length')]
    #[Groups(['trip:read', 'trip:write'])]
    private ?string $description = null;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'trips')]
    #[Assert\Count(min: 1, minMessage: 'trip.owner.min_count')]
    #[Groups(['trip:read', 'trip:write'])]
    private Collection $owners;

    public function __construct(#[ORM\Column]
        #[Groups(['trip:read'])]
        private ?\DateTimeImmutable $createdAt = new \DateTimeImmutable())
    {
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getStartAt(): ?\DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeImmutable $endAt): static
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return RequiredLevel[]
     */
    public function getRequiredLevels(): array
    {
        return $this->requiredLevels;
    }

    /**
     * @param RequiredLevel[] $requiredLevels
     */
    public function setRequiredLevels(array $requiredLevels): static
    {
        $this->requiredLevels = $requiredLevels;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getOwners(): Collection
    {
        return $this->owners;
    }

    public function addOwner(User $owner): static
    {
        if (!$this->owners->contains($owner)) {
            $this->owners->add($owner);
        }

        return $this;
    }

    public function removeOwner(User $owner): static
    {
        $this->owners->removeElement($owner);

        return $this;
    }
}
