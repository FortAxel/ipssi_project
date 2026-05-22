<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\ReadingProgress;
use App\Entity\User;
use App\Repository\ReadingProgressRepository;
use DateTimeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reading-history')]
final class ReadingHistoryController extends AbstractController
{
    public function __construct(
        private readonly ReadingProgressRepository $progressRepository,
    ) {
    }

    #[Route('', name: 'api_reading_history_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $user = $this->requireUser();
        $entries = $this->progressRepository->findHistoryByUser($user);

        return $this->json([
            'items' => array_map(
                static fn (ReadingProgress $rp): array => [
                    'storyId' => $rp->getStory()->getId(),
                    'title' => $rp->getStory()->getTitle(),
                    'lastPageNumber' => $rp->getLastPageNumber(),
                    'pageCount' => $rp->getStory()->getPages()->count(),
                    'isCompleted' => $rp->getIsCompleted(),
                    'startedAt' => $rp->getStartedAt()->format(DateTimeInterface::ATOM),
                    'lastReadAt' => $rp->getLastReadAt()->format(DateTimeInterface::ATOM),
                ],
                $entries,
            ),
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
