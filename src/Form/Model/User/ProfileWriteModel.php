<?php

declare(strict_types=1);

namespace App\Form\Model\User;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\LastName;
use App\Entity\ValueObject\Username;
use App\Enum\User\SurfLevel;
use Symfony\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints as Assert;

#[Map(source: User::class)]
#[Map(target: User::class)]
final class ProfileWriteModel
{
    public Email $email;

    public Username $username;

    public FirstName $firstName;

    public ?LastName $lastName = null;

    public ?string $avatar = null;

    public ?SurfLevel $level = null;

    #[Assert\Length(max: 255, maxMessage: 'user.location.max_length')]
    public ?string $location = null;

    #[Assert\Regex(pattern: '/^(?:@)?[A-Za-z0-9._]{1,30}$/', message: 'user.instagram.invalid_format')]
    public ?string $instagram = null;

    #[Assert\Length(max: 1000, maxMessage: 'user.description.max_length')]
    public ?string $description = null;
}
