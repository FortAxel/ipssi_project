<?php

declare(strict_types=1);

namespace App\Entity;

use App\Enum\StoryCategory;
use App\Enum\StoryStatus;
use App\Repository\StoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title = '';

    #[ORM\Column(type: 'text')]
    private string $description = '';

    #[ORM\Column(length: 512)]
    private string $coverImage = '';

    #[ORM\Column(enumType: StoryStatus::class)]
    private StoryStatus $status = StoryStatus::Draft;

    #[ORM\Column(enumType: StoryCategory::class)]
    private StoryCategory $category = StoryCategory::Other;

    #[ORM\Column(length: 32)]
    private string $ageRange = '3-6';

    /** @var Collection<int, Page> */
    #[ORM\OneToMany(targetEntity: Page::class, mappedBy: 'story', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['pageNumber' => 'ASC'])]
    private Collection $pages;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    #[ORM\Column]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->pages = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getCoverImage(): string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getStatus(): StoryStatus
    {
        return $this->status;
    }

    public function setStatus(StoryStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCategory(): StoryCategory
    {
        return $this->category;
    }

    public function setCategory(StoryCategory $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getAgeRange(): string
    {
        return $this->ageRange;
    }

    public function setAgeRange(string $ageRange): static
    {
        $this->ageRange = $ageRange;

        return $this;
    }

    /** @return Collection<int, Page> */
    public function getPages(): Collection
    {
        return $this->pages;
    }

    public function addPage(Page $page): static
    {
        if (!$this->pages->contains($page)) {
            $this->pages->add($page);
            $page->setStory($this);
        }

        return $this;
    }

    public function removePage(Page $page): static
    {
        if ($this->pages->removeElement($page) && $page->getStory() === $this) {
            $page->setStory(null);
        }

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
