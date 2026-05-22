<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Page;
use App\Entity\Story;
use DateTimeInterface;

final class StorySerializer
{
    /**
     * @return array<string, mixed>
     */
    /**
     * @param array{isFavorite?: bool, lastPageNumber?: int} $extra
     *
     * @return array<string, mixed>
     */
    public function toSummary(Story $story, array $extra = []): array
    {
        return array_merge([
            'id' => $story->getId(),
            'title' => $story->getTitle(),
            'description' => $story->getDescription(),
            'coverImage' => $story->getCoverImage(),
            'category' => $story->getCategory()->value,
            'ageRange' => $story->getAgeRange(),
            'pageCount' => $story->getPages()->count(),
        ], $extra);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDetail(Story $story, bool $includeAdminFields = false): array
    {
        $data = $this->toSummary($story);
        $data['pages'] = array_map(
            fn (Page $page): array => $this->pageToArray($page),
            $story->getPages()->toArray(),
        );

        if ($includeAdminFields) {
            $data['status'] = $story->getStatus()->value;
            $data['createdAt'] = $story->getCreatedAt()->format(DateTimeInterface::ATOM);
            $data['updatedAt'] = $story->getUpdatedAt()->format(DateTimeInterface::ATOM);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    public function pageToArray(Page $page): array
    {
        return [
            'id' => $page->getId(),
            'pageNumber' => $page->getPageNumber(),
            'content' => $page->getContent(),
            'illustration' => $page->getIllustration(),
        ];
    }
}
