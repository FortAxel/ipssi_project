<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ReadingProgress;
use App\Entity\Story;
use App\Entity\User;
use App\Enum\StoryStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ReadingProgress>
 */
class ReadingProgressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadingProgress::class);
    }

    public function findOneByUserAndStory(User $user, Story $story): ?ReadingProgress
    {
        return $this->findOneBy(['user' => $user, 'story' => $story]);
    }

    /**
     * @return array<int, int> storyId => lastPageNumber
     */
    public function mapByUser(User $user): array
    {
        $rows = $this->createQueryBuilder('rp')
            ->select('IDENTITY(rp.story) AS storyId', 'rp.lastPageNumber')
            ->where('rp.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getArrayResult();

        $map = [];
        foreach ($rows as $row) {
            $storyId = (int) ($row['storyId'] ?? $row['story_id'] ?? 0);
            $lastPage = (int) ($row['lastPageNumber'] ?? $row['last_page_number'] ?? 0);
            if ($storyId > 0) {
                $map[$storyId] = $lastPage;
            }
        }

        return $map;
    }

    /** @return list<ReadingProgress> */
    public function findHistoryByUser(User $user): array
    {
        /** @var list<ReadingProgress> $rows */
        $rows = $this->createQueryBuilder('rp')
            ->innerJoin('rp.story', 's')
            ->addSelect('s')
            ->where('rp.user = :user')
            ->andWhere('s.status = :published')
            ->setParameter('user', $user)
            ->setParameter('published', StoryStatus::Published)
            ->orderBy('rp.lastReadAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $rows;
    }
}
