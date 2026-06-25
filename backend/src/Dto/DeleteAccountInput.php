<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class DeleteAccountInput
{
    #[Assert\NotBlank(message: 'Le mot de passe est requis pour supprimer le compte.')]
    public string $currentPassword = '';
}
