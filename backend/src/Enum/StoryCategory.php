<?php

declare(strict_types=1);

namespace App\Enum;

enum StoryCategory: string
{
    case Adventure = 'ADVENTURE';
    case Animals = 'ANIMALS';
    case Family = 'FAMILY';
    case Fantasy = 'FANTASY';
    case Other = 'OTHER';
}
