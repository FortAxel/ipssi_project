<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class StoryInput
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $title = '';

    #[Assert\NotBlank]
    public string $description = '';

    #[Assert\NotBlank]
    #[Assert\Length(max: 512)]
    public string $coverImage = '';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['DRAFT', 'PUBLISHED', 'ARCHIVED'])]
    public string $status = 'DRAFT';

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['ADVENTURE', 'ANIMALS', 'FAMILY', 'FANTASY', 'OTHER'])]
    public string $category = 'OTHER';

    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    public string $ageRange = '3-6';
}
