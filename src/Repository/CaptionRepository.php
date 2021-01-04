<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Caption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UserBundle\Entity\User;

/**
 * CaptionRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CaptionRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Caption::class);
    }

    public function findCaptionsQuery(?User $user = null) {
        $qb = $this->createQueryBuilder('c');
        $qb->orderBy('c.id');
        if ( ! $user) {
            $qb->innerJoin('c.video', 'v');
            $qb->andWhere('v.hidden = 0');
        }

        return $qb->getQuery();
    }
}
