<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class UpdateProfileInput
{
    #[Assert\NotBlank(message: 'Le mot de passe actuel est requis.')]
    public string $currentPassword = '';

    #[Assert\Email(message: 'Adresse e-mail invalide.')]
    public ?string $email = null;

    #[Assert\Length(min: 8, max: 128, minMessage: 'Le nouveau mot de passe doit contenir au moins 8 caractères.')]
    public ?string $newPassword = null;

    #[Assert\Callback]
    public function validateAtLeastOneChange(ExecutionContextInterface $context): void
    {
        $email = $this->email !== null ? trim($this->email) : '';
        $hasEmail = $email !== '';
        $hasPassword = $this->newPassword !== null && $this->newPassword !== '';

        if (!$hasEmail && !$hasPassword) {
            $context->buildViolation('Indiquez une nouvelle adresse e-mail ou un nouveau mot de passe.')
                ->addViolation();
        }
    }
}
