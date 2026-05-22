<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReadingProgressRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReadingProgressRepository::class)]
#[ORM\Table(name: 'reading_progress')]
#[ORM\UniqueConstraint(name: 'uniq_progress_user_story', columns: ['user_id', 'story_id'])]
class ReadingProgress
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\ManyToOne(targetEntity: Story::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Story $story;

    #[ORM\Column(name: 'last_page_number')]
    private int $lastPageNumber = 1;

    #[ORM\Column(name: 'is_completed')]
    private bool $isCompleted = false;

    #[ORM\Column(name: 'started_at')]
    private DateTimeImmutable $startedAt;

    #[ORM\Column(name: 'last_read_at')]
    private DateTimeImmutable $lastReadAt;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->startedAt = $now;
        $this->lastReadAt = $now;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getStory(): Story
    {
        return $this->story;
    }

    public function setStory(Story $story): static
    {
        $this->story = $story;

        return $this;
    }

    public function getLastPageNumber(): int
    {
        return $this->lastPageNumber;
    }

    public function getIsCompleted(): bool
    {
        return $this->isCompleted;
    }

    public function getStartedAt(): DateTimeImmutable
    {
        return $this->startedAt;
    }

    public function getLastReadAt(): DateTimeImmutable
    {
        return $this->lastReadAt;
    }

    public function updateProgress(int $lastPageNumber, int $pageCount): void
    {
        $this->lastPageNumber = max(1, min($lastPageNumber, max(1, $pageCount)));
        $this->isCompleted = $this->lastPageNumber >= max(1, $pageCount);
        $this->lastReadAt = new DateTimeImmutable();
    }
}
