<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Dto\PageInput;
use App\Dto\StoryInput;
use App\Entity\Page;
use App\Entity\Story;
use App\Enum\StoryCategory;
use App\Enum\StoryStatus;
use App\Repository\PageRepository;
use App\Repository\StoryRepository;
use App\Service\StorySerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin/stories')]
final class AdminStoryController extends AbstractController
{
    public function __construct(
        private readonly StoryRepository $storyRepository,
        private readonly PageRepository $pageRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly StorySerializer $storySerializer,
    ) {
    }

    #[Route('', name: 'api_admin_stories_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $stories = $this->storyRepository->findAllForAdmin();

        return $this->json([
            'items' => array_map(
                fn (Story $story) => $this->storySerializer->toDetail($story, true),
                $stories,
            ),
        ]);
    }

    #[Route('/{id}', name: 'api_admin_stories_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $story = $this->storyRepository->findOneForAdmin($id);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($this->storySerializer->toDetail($story, true));
    }

    #[Route('', name: 'api_admin_stories_create', methods: ['POST'])]
    public function create(#[MapRequestPayload] StoryInput $input): JsonResponse
    {
        $story = $this->applyStoryInput(new Story(), $input);
        $this->entityManager->persist($story);
        $this->entityManager->flush();

        return $this->json($this->storySerializer->toDetail($story, true), Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'api_admin_stories_update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(int $id, #[MapRequestPayload] StoryInput $input): JsonResponse
    {
        $story = $this->storyRepository->findOneForAdmin($id);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->applyStoryInput($story, $input);
        $this->entityManager->flush();

        return $this->json($this->storySerializer->toDetail($story, true));
    }

    #[Route('/{id}', name: 'api_admin_stories_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $story = $this->storyRepository->find($id);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($story);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{storyId}/pages', name: 'api_admin_pages_create', requirements: ['storyId' => '\d+'], methods: ['POST'])]
    public function createPage(int $storyId, #[MapRequestPayload] PageInput $input): JsonResponse
    {
        $story = $this->storyRepository->find($storyId);
        if ($story === null) {
            return $this->json(['error' => 'not_found', 'message' => 'Histoire introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $page = $this->applyPageInput(new Page(), $input);
        $story->addPage($page);
        $this->entityManager->flush();

        return $this->json($this->storySerializer->pageToArray($page), Response::HTTP_CREATED);
    }

    #[Route('/{storyId}/pages/{pageId}', name: 'api_admin_pages_update', requirements: ['storyId' => '\d+', 'pageId' => '\d+'], methods: ['PUT'])]
    public function updatePage(int $storyId, int $pageId, #[MapRequestPayload] PageInput $input): JsonResponse
    {
        $page = $this->pageRepository->find($pageId);
        if ($page === null || $page->getStory()?->getId() !== $storyId) {
            return $this->json(['error' => 'not_found', 'message' => 'Page introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->applyPageInput($page, $input);
        $this->entityManager->flush();

        return $this->json($this->storySerializer->pageToArray($page));
    }

    #[Route('/{storyId}/pages/{pageId}', name: 'api_admin_pages_delete', requirements: ['storyId' => '\d+', 'pageId' => '\d+'], methods: ['DELETE'])]
    public function deletePage(int $storyId, int $pageId): JsonResponse
    {
        $page = $this->pageRepository->find($pageId);
        if ($page === null || $page->getStory()?->getId() !== $storyId) {
            return $this->json(['error' => 'not_found', 'message' => 'Page introuvable.'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($page);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    private function applyStoryInput(Story $story, StoryInput $input): Story
    {
        return $story
            ->setTitle($input->title)
            ->setDescription($input->description)
            ->setCoverImage($input->coverImage)
            ->setStatus(StoryStatus::from($input->status))
            ->setCategory(StoryCategory::from($input->category))
            ->setAgeRange($input->ageRange);
    }

    private function applyPageInput(Page $page, PageInput $input): Page
    {
        return $page
            ->setPageNumber($input->pageNumber)
            ->setContent($input->content)
            ->setIllustration($input->illustration);
    }
}
