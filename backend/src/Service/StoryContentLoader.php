<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Page;
use App\Entity\Story;
use App\Enum\StoryCategory;
use App\Enum\StoryStatus;

/**
 * Loads stories from JSON files in backend/data/stories (seed content).
 */
final class StoryContentLoader
{
    private string $storiesPath;

    public function __construct(string $projectDir)
    {
        $this->storiesPath = $projectDir.'/data/stories';
    }

    /**
     * @return list<array{story: Story, pages: list<Page>}>
     */
    public function loadAll(): array
    {
        if (!is_dir($this->storiesPath)) {
            return [];
        }

        $files = glob($this->storiesPath.'/story-*.json') ?: [];
        sort($files);

        $result = [];
        foreach ($files as $file) {
            $result[] = $this->loadFile($file);
        }

        return $result;
    }

    /**
     * @return array{story: Story, pages: list<Page>}
     */
    private function loadFile(string $file): array
    {
        /** @var array{story: array<string, mixed>} $data */
        $data = json_decode((string) file_get_contents($file), true, 512, \JSON_THROW_ON_ERROR);
        $raw = $data['story'];

        $title = (string) ($raw['title'] ?? '');
        $summary = (string) ($raw['summary'] ?? '');
        $ageMin = (int) ($raw['ageMinRecommands'] ?? 3);
        $ageMax = (int) ($raw['ageMaxRecommands'] ?? 7);
        $content = (string) ($raw['content'] ?? '');
        /** @var list<array{position: int, url: string}> $images */
        $images = $raw['images'] ?? [];

        $imageByPosition = [];
        foreach ($images as $image) {
            $imageByPosition[(int) $image['position']] = $this->toPublicImagePath((string) $image['url']);
        }

        $coverImage = $imageByPosition[0]
            ?? ($imageByPosition !== [] ? (string) reset($imageByPosition) : '');

        $story = (new Story())
            ->setTitle($title)
            ->setDescription($summary)
            ->setCoverImage($coverImage)
            ->setStatus(StoryStatus::Published)
            ->setCategory($this->guessCategory($title))
            ->setAgeRange(\sprintf('%d-%d', $ageMin, $ageMax));

        $paragraphs = array_values(array_filter(
            array_map(trim(...), preg_split('/\n\s*\n/', $content) ?: []),
            static fn (string $p): bool => $p !== '',
        ));

        $pages = [];
        $pageNumber = 1;
        foreach ($paragraphs as $paragraph) {
            $illustration = $imageByPosition[$pageNumber - 1]
                ?? $imageByPosition[array_key_last($imageByPosition)]
                ?? $coverImage;

            $page = (new Page())
                ->setPageNumber($pageNumber)
                ->setContent($paragraph)
                ->setIllustration($illustration);
            $story->addPage($page);
            $pages[] = $page;
            ++$pageNumber;
        }

        return ['story' => $story, 'pages' => $pages];
    }

    private function toPublicImagePath(string $url): string
    {
        if (str_starts_with($url, '/images/')) {
            return $url;
        }

        return '/images/'.basename($url);
    }

    private function guessCategory(string $title): StoryCategory
    {
        $lower = mb_strtolower($title);

        if (preg_match('/fée|fées|magique|enchant|secret|château|forêt/i', $lower)) {
            return StoryCategory::Fantasy;
        }
        if (preg_match('/ferme|animaux|dinosaure|jungle/i', $lower)) {
            return StoryCategory::Animals;
        }
        if (preg_match('/pirate|aventure|trésor|robot/i', $lower)) {
            return StoryCategory::Adventure;
        }
        if (preg_match('/famille|amitié|ami/i', $lower)) {
            return StoryCategory::Family;
        }

        return StoryCategory::Other;
    }
}
