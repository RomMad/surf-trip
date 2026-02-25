<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\RequiredLevel;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'trip.title.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'trip.title.max_length')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'trip.location.not_blank')]
    #[Assert\Length(max: 255, maxMessage: 'trip.location.max_length')]
    private ?string $location = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'trip.start_at.not_null')]
    private ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Assert\NotNull(message: 'trip.end_at.not_null')]
    #[Assert\Expression(
        expression: 'value >= this.getStartAt()',
        message: 'trip.end_at.before_start_at'
    )]
    private ?\DateTimeImmutable $endAt = null;

    /** @var RequiredLevel[] */
    #[ORM\Column(type: Types::JSONB, enumType: RequiredLevel::class)]
    #[Assert\Count(min: 1, minMessage: 'trip.required_levels.min_count')]
    #[Assert\All([
        new Assert\Type(type: RequiredLevel::class, message: 'trip.required_levels.invalid_type'),
    ])]
    private array $requiredLevels = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(max: 5000, maxMessage: 'trip.description.max_length')]
    private ?string $description = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'trips')]
    #[Assert\Count(min: 1, minMessage: 'trip.owner.min_count')]
    private Collection $owners;

    public function __construct()
    {
        $this->owners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getLocation(): ?string
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

    public function setStartAt(\DateTimeImmutable $startAt): static
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): static
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
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
