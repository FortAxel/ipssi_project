<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use DateTimeInterface;

final class UserSerializer
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(User $user): array
    {
        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(User $user): array
    {
        $roles = $user->getRoles();

        return [
            'id' => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'email' => $user->getEmail(),
            'roles' => $roles,
            'isAdmin' => \in_array('ROLE_ADMIN', $roles, true),
            'createdAt' => $user->getCreatedAt()->format(DateTimeInterface::ATOM),
            'updatedAt' => $user->getUpdatedAt()->format(DateTimeInterface::ATOM),
        ];
    }
}
