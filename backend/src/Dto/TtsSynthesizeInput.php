<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class TtsSynthesizeInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 5000)]
    public string $text = '';
}
