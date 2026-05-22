<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class FavoriteToggleInput
{
    #[Assert\Positive]
    public int $storyId = 0;
}
