<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Channel;
use App\Entity\Figuration;
use App\Entity\Keyword;
use App\Entity\Playlist;
use App\Entity\Video;
use App\Entity\VideoProfile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Nines\UserBundle\Entity\User;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * VideoRepository
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VideoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Video::class);
    }

    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.title LIKE :q');
        $qb->orderBy('e.title');
        $qb->setParameter('q', "%{$q}%");

        return $qb->getQuery()->execute();
    }

    public function findVideosWithoutProfile(User $user) {
        $subQB = $this->_em->createQueryBuilder();
        $subQB->select('IDENTITY(vp.video)');
        $subQB->from('App:VideoProfile', 'vp');
        $subQB->where('vp.user = :user');

        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->notIn('v.id', $subQB->getDQL()));
        $qb->setParameter('user', $user);

        return $qb->getQuery();
    }

    public function findVideosWithProfile(User $user) {
        $subQB = $this->_em->createQueryBuilder();
        $subQB->select('IDENTITY(vp.video)');
        $subQB->from('App:VideoProfile', 'vp');
        $subQB->where('vp.user = :user');
        $subQB->setParameter('user', $user);

        $qb = $this->createQueryBuilder('v');
        $qb->where($qb->expr()->in('v.id', $subQB->getDQL()));

        return $qb->getQuery();
    }

    public function findVideosQuery(?User $user = null, $opts = []) {
        $qb = $this->createQueryBuilder('e');
        if (null === $user) {
            $qb->andWhere('e.hidden = 0');
        }
        $qb->orderBy('e.id');

        if (isset($opts['type'])) {
            switch ($opts['type']) {
                case Channel::class:
                    $qb->andWhere('e.channel = :id');

                    break;
                case Keyword::class:
                    $qb->innerJoin('e.keywords', 'k')
                        ->andWhere('k.id = :id')
                    ;

                    break;
                case Playlist::class:
                    $qb->innerJoin('e.playlists', 'p')
                        ->andWhere('p.id = :id')
                    ;

                    break;
                case Figuration::class:
                    $qb->andWhere('e.figuration = :id');

                    break;
                case VideoProfile::class:
                    $qb->innerJoin('e.videoProfiles', 'vp')
                        ->innerJOin('vp.profileKeywords', 'pk')
                        ->andWhere('pk.id = :id')
                    ;

                    break;
                default:
                    throw new HttpException(500, 'Unknown filter type ' . $opts['type']);
            }
            $qb->setParameter('id', $opts['id']);
        }

        return $qb->getQuery();
    }
}
