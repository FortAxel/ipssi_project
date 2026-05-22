<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Story;
use App\Enum\StoryCategory;
use App\Enum\StoryStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Story>
 */
class StoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Story::class);
    }

    /**
     * @return list<Story>
     */
    public function findPublished(?string $search = null, ?string $category = null): array
    {
        $qb = $this->createQueryBuilder('s')
            ->andWhere('s.status = :status')
            ->setParameter('status', StoryStatus::Published)
            ->orderBy('s.title', 'ASC');

        if ($search !== null && $search !== '') {
            $qb->andWhere('s.title LIKE :search OR s.description LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        if ($category !== null && $category !== '') {
            $qb->andWhere('s.category = :category')
                ->setParameter('category', StoryCategory::from($category));
        }

        return $qb->getQuery()->getResult();
    }

    public function findPublishedWithPages(int $id): ?Story
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.pages', 'p')
            ->addSelect('p')
            ->andWhere('s.id = :id')
            ->andWhere('s.status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', StoryStatus::Published)
            ->orderBy('p.pageNumber', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return list<Story>
     */
    public function findAllForAdmin(): array
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.pages', 'p')
            ->addSelect('p')
            ->orderBy('s.updatedAt', 'DESC')
            ->addOrderBy('p.pageNumber', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findOneForAdmin(int $id): ?Story
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.pages', 'p')
            ->addSelect('p')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->orderBy('p.pageNumber', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
