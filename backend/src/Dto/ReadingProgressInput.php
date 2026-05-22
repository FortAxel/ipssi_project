<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class ReadingProgressInput
{
    #[Assert\Positive]
    public int $lastPageNumber = 1;
}
