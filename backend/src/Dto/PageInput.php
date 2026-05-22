<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class PageInput
{
    #[Assert\Positive]
    public int $pageNumber = 1;

    #[Assert\NotBlank]
    public string $content = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 512)]
    public string $illustration = '';
}
