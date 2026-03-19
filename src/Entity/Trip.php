<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\FreeTextQueryFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrFilter;
use ApiPlatform\Doctrine\Orm\Filter\PartialSearchFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\QueryParameter;
use App\Doctrine\Type\LocationType;
use App\Doctrine\Type\SlugType;
use App\Doctrine\Type\TitleType;
use App\Entity\ValueObject\Location;
use App\Entity\ValueObject\Slug;
use App\Entity\ValueObject\Title;
use App\Enum\Trip\RequiredLevel;
use App\Filter\JsonContainsFilter;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ORM\Index(name: 'idx_trip_location', fields: ['location'])]
#[ORM\Index(name: 'idx_trip_required_levels', fields: ['requiredLevels'], flags: ['gin'])]
#[ORM\Index(name: 'idx_trip_search', fields: ['title', 'location'])]
#[ORM\Index(name: 'idx_trip_start_at', fields: ['startAt'])]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['trip:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['trip:read']],
            parameters: [
                'q' => new QueryParameter(
                    filter: new FreeTextQueryFilter(new OrFilter(new PartialSearchFilter())),
                    properties: ['title', 'location']
                ),
                'levels' => new QueryParameter(
                    filter: new JsonContainsFilter(),
                    property: 'requiredLevels'
                ),
            ],
        ),
        new Patch(
            denormalizationContext: ['groups' => ['trip:write']],
            security: 'is_granted("EDIT", object)',
        ),
        new Post(
            denormalizationContext: ['groups' => ['trip:write']],
            security: 'is_granted("ROLE_USER")',
        ),
        new Delete(
            security: 'is_granted("DELETE", object)',
        ),
    ]
)]
final class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['trip:read'])]
    public private(set) ?int $id = null;

    #[ORM\Column(type: TitleType::NAME)]
    #[Groups(['trip:read', 'trip:write'])]
    public Title $title;

    #[ORM\Column(type: LocationType::NAME)]
    #[Groups(['trip:read', 'trip:write'])]
    public Location $location;

    #[ORM\Column]
    #[Groups(['trip:read', 'trip:write'])]
    public ?\DateTimeImmutable $startAt = null;

    #[ORM\Column]
    #[Groups(['trip:read', 'trip:write'])]
    public ?\DateTimeImmutable $endAt = null;

    /** @var RequiredLevel[] */
    #[ORM\Column(type: Types::JSONB, enumType: RequiredLevel::class)]
    #[Groups(['trip:read', 'trip:write'])]
    public array $requiredLevels = [];

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['trip:read', 'trip:write'])]
    public ?string $description = null;

    #[ORM\Column(type: SlugType::NAME, nullable: false)]
    #[Groups(['trip:read'])]
    public Slug $slug;

    /** @var Collection<int, User> */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'trips')]
    #[Groups(['trip:read', 'trip:write'])]
    public private(set) Collection $owners;

    public function __construct(
        #[ORM\Column]
        #[Groups(['trip:read'])]
        public private(set) \DateTimeImmutable $createdAt = new \DateTimeImmutable()
    ) {
        $this->owners = new ArrayCollection();
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

    /**
     * @param Collection<int, User> $users
     */
    public function setOwners(Collection $users): void
    {
        foreach ($this->owners as $owner) {
            if (!$users->contains($owner)) {
                $this->removeOwner($owner);
            }
        }

        foreach ($users as $owner) {
            $this->addOwner($owner);
        }
    }
}
