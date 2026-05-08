<?php

declare(strict_types=1);

namespace App\Form\Model\User;

use App\Entity\User;
use App\Entity\ValueObject\Email;
use App\Entity\ValueObject\FirstName;
use App\Entity\ValueObject\Username;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\ObjectMapper\Attribute\Map;

#[UniqueEntity(fields: ['email'], message: 'user.email.unique', entityClass: User::class)]
#[UniqueEntity(fields: ['username'], message: 'user.username.unique', entityClass: User::class)]
#[Map(target: User::class)]
final class RegistrationWriteModel
{
    public ?Email $email = null;

    public ?Username $username = null;

    public ?FirstName $firstName = null;
}
