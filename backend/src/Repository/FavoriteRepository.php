<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Favorite;
use App\Entity\Story;
use App\Entity\User;
use App\Enum\StoryStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favorite>
 */
class FavoriteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favorite::class);
    }

    public function findOneByUserAndStory(User $user, Story $story): ?Favorite
    {
        return $this->findOneBy(['user' => $user, 'story' => $story]);
    }

    /** @return list<int> */
    public function findStoryIdsByUser(User $user): array
    {
        $rows = $this->createQueryBuilder('f')
            ->select('IDENTITY(f.story) AS storyId')
            ->where('f.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getScalarResult();

        return array_map(
            static fn (array $row): int => (int) ($row['storyId'] ?? $row['story_id'] ?? 0),
            $rows,
        );
    }

    /** @return list<Story> */
    public function findStoriesByUser(User $user): array
    {
        /** @var list<Favorite> $favorites */
        $favorites = $this->createQueryBuilder('f')
            ->innerJoin('f.story', 's')
            ->addSelect('s')
            ->where('f.user = :user')
            ->andWhere('s.status = :published')
            ->setParameter('user', $user)
            ->setParameter('published', StoryStatus::Published)
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return array_values(array_map(static fn (Favorite $f) => $f->getStory(), $favorites));
    }
}
