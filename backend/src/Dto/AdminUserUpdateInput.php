<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class AdminUserUpdateInput
{
    #[Assert\Length(max: 100)]
    public ?string $firstName = null;

    #[Assert\Length(max: 100)]
    public ?string $lastName = null;

    #[Assert\Email]
    public ?string $email = null;

    #[Assert\Length(min: 8, max: 128)]
    public ?string $password = null;

    public ?bool $isAdmin = null;
}
