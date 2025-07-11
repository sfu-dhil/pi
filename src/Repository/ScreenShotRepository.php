<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ScreenShot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * ScreenShotRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ScreenShotRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, ScreenShot::class);
    }

    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.originalName LIKE :q');
        $qb->orderBy('e.originalName');
        $qb->setParameter('q', "{$q}%");

        return $qb->getQuery()->execute();
    }

    public function searchQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect('MATCH (e.originalName) AGAINST(:q BOOLEAN) as HIDDEN score');
        $qb->setParameter('q', $q);

        return $qb->getQuery();
    }
}
