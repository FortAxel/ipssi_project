<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\FavoriteToggleInput;
use App\Entity\Favorite;
use App\Entity\User;
use App\Repository\FavoriteRepository;
use App\Repository\ReadingProgressRepository;
use App\Repository\StoryRepository;
use App\Service\StorySerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/favorites')]
final class FavoriteController extends AbstractController
{
    public function __construct(
        private readonly FavoriteRepository $favoriteRepository,
        private readonly ReadingProgressRepository $progressRepository,
        private readonly StoryRepository $storyRepository,
        private readonly StorySerializer $storySerializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('', name: 'api_favorites_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->requireUser();

        $stories = $this->favoriteRepository->findStoriesByUser($user);
        $progressMap = $this->progressRepository->mapByUser($user);

        return $this->json([
            'items' => array_map(function ($story) use ($progressMap) {
                $id = $story->getId();

                return $this->storySerializer->toSummary($story, [
                    'isFavorite' => true,
                    'lastPageNumber' => $progressMap[$id] ?? 0,
                ]);
            }, $stories),
        ]);
    }

    #[Route('/toggle', name: 'api_favorites_toggle', methods: ['POST'])]
    public function toggle(#[MapRequestPayload] FavoriteToggleInput $input): JsonResponse
    {
        $user = $this->requireUser();
        $story = $this->storyRepository->findPublishedWithPages($input->storyId);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $existing = $this->favoriteRepository->findOneByUserAndStory($user, $story);
        if ($existing !== null) {
            $this->entityManager->remove($existing);
            $this->entityManager->flush();

            return $this->json(['storyId' => $story->getId(), 'isFavorite' => false]);
        }

        $favorite = (new Favorite())->setUser($user)->setStory($story);
        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        return $this->json(['storyId' => $story->getId(), 'isFavorite' => true]);
    }

    private function requireUser(): User
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        return $user;
    }
}
