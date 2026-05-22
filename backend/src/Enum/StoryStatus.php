<?php

declare(strict_types=1);

namespace App\Enum;

enum StoryStatus: string
{
    case Draft = 'DRAFT';
    case Published = 'PUBLISHED';
    case Archived = 'ARCHIVED';
}
