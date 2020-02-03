<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Figuration;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Figuration controller.
 *
 * @Route("/figuration")
 */
class FigurationController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Figuration entities.
     *
     * @return array
     *
     * @Route("/", name="figuration_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Figuration::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();

        $figurations = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'figurations' => $figurations,
            'repo' => $em->getRepository(Figuration::class),
        ];
    }

    /**
     * Finds and displays a Figuration entity.
     *
     * @return array
     *
     * @Route("/{id}", name="figuration_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Figuration $figuration, VideoRepository $repo) {
        $videos = $repo->findVideosQuery($this->getUser(), [
            'type' => Figuration::class,
            'id' => $figuration->getId(),
        ]);

        return [
            'figuration' => $figuration,
            'videos' => $videos->execute(),
        ];
    }
}
