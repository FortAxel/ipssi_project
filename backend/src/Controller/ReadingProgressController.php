<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\ReadingProgressInput;
use App\Entity\ReadingProgress;
use App\Entity\User;
use App\Repository\ReadingProgressRepository;
use App\Repository\StoryRepository;
use DateTimeInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reading-progress')]
final class ReadingProgressController extends AbstractController
{
    public function __construct(
        private readonly ReadingProgressRepository $progressRepository,
        private readonly StoryRepository $storyRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/{storyId}', name: 'api_reading_progress_show', requirements: ['storyId' => '\d+'], methods: ['GET'])]
    public function show(int $storyId): JsonResponse
    {
        $user = $this->requireUser();
        $story = $this->storyRepository->findPublishedWithPages($storyId);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $progress = $this->progressRepository->findOneByUserAndStory($user, $story);

        return $this->json([
            'storyId' => $storyId,
            'lastPageNumber' => $progress?->getLastPageNumber() ?? 1,
            'pageCount' => $story->getPages()->count(),
            'isCompleted' => $progress?->getIsCompleted() ?? false,
            'startedAt' => $progress?->getStartedAt()->format(DateTimeInterface::ATOM) ?? null,
            'lastReadAt' => $progress?->getLastReadAt()->format(DateTimeInterface::ATOM) ?? null,
        ]);
    }

    #[Route('/{storyId}', name: 'api_reading_progress_save', requirements: ['storyId' => '\d+'], methods: ['PUT'])]
    public function save(int $storyId, #[MapRequestPayload] ReadingProgressInput $input): JsonResponse
    {
        $user = $this->requireUser();
        $story = $this->storyRepository->findPublishedWithPages($storyId);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $pageCount = max(1, $story->getPages()->count());
        $page = min(max(1, $input->lastPageNumber), $pageCount);

        $progress = $this->progressRepository->findOneByUserAndStory($user, $story);
        if ($progress === null) {
            $progress = (new ReadingProgress())->setUser($user)->setStory($story);
            $this->entityManager->persist($progress);
        }

        $progress->updateProgress($page, $pageCount);
        $this->entityManager->flush();

        return $this->json([
            'storyId' => $storyId,
            'lastPageNumber' => $progress->getLastPageNumber(),
            'pageCount' => $pageCount,
            'isCompleted' => $progress->getIsCompleted(),
            'lastReadAt' => $progress->getLastReadAt()->format(DateTimeInterface::ATOM),
        ]);
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
