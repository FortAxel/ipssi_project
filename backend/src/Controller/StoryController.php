<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Enum\StoryCategory;
use App\Repository\FavoriteRepository;
use App\Repository\ReadingProgressRepository;
use App\Repository\StoryRepository;
use App\Service\StorySerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use ValueError;

#[Route('/api/stories')]
final class StoryController extends AbstractController
{
    public function __construct(
        private readonly StoryRepository $storyRepository,
        private readonly StorySerializer $storySerializer,
        private readonly FavoriteRepository $favoriteRepository,
        private readonly ReadingProgressRepository $progressRepository,
    ) {
    }

    #[Route('', name: 'api_stories_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $search = $request->query->getString('search', '');
        $search = $search === '' ? null : $search;
        $category = $request->query->getString('category', '');
        $category = $category === '' ? null : $category;

        if ($category !== null) {
            try {
                StoryCategory::from($category);
            } catch (ValueError) {
                return $this->json([
                    'error' => 'invalid_category',
                    'message' => 'Catégorie invalide.',
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $stories = $this->storyRepository->findPublished($search, $category);
        $user = $this->getUser();
        $favoriteIds = [];
        $progressMap = [];
        if ($user instanceof User) {
            $favoriteIds = $this->favoriteRepository->findStoryIdsByUser($user);
            $progressMap = $this->progressRepository->mapByUser($user);
        }

        return $this->json([
            'items' => array_map(function ($story) use ($favoriteIds, $progressMap) {
                $id = $story->getId();

                return $this->storySerializer->toSummary($story, [
                    'isFavorite' => \in_array($id, $favoriteIds, true),
                    'lastPageNumber' => $progressMap[$id] ?? 0,
                ]);
            }, $stories),
        ]);
    }

    #[Route('/{id}', name: 'api_stories_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $story = $this->storyRepository->findPublishedWithPages($id);
        if ($story === null) {
            return $this->json([
                'error' => 'not_found',
                'message' => 'Histoire introuvable.',
            ], Response::HTTP_NOT_FOUND);
        }

        $data = $this->storySerializer->toDetail($story);
        $user = $this->getUser();
        if ($user instanceof User) {
            $data['isFavorite'] = $this->favoriteRepository->findOneByUserAndStory($user, $story) !== null;
            $progress = $this->progressRepository->findOneByUserAndStory($user, $story);
            $data['lastPageNumber'] = $progress?->getLastPageNumber() ?? 1;
        }

        return $this->json($data);
    }
}
