<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

trait CreatedByTrait
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read'])]
    public ?User $createdBy = null;

    public function setCreatedBy(User $user): static
    {
        $this->createdBy ??= $user;

        return $this;
    }
}
