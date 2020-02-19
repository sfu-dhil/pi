<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Figuration;
use AppBundle\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Figuration controller.
 *
 * @Route("/figuration")
 */
class FigurationController extends Controller {
    /**
     * Lists all Figuration entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="figuration_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Figuration::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $figurations = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'figurations' => $figurations,
            'repo' => $em->getRepository(Figuration::class),
        );
    }

    /**
     * Finds and displays a Figuration entity and paginates it
     *
     * @param Request $request
     *
     * @param Figuration $figuration
     *
     * @return array
     *
     * @Route("/{id}", name="figuration_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Request $request, Figuration $figuration) {
        $repo = $this->getDoctrine()->getManager()->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), array(
            'type' => Figuration::class,
            'id' => $figuration->getId(),
        ));
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 20);
        return array(
            'figuration' => $figuration,
            'videos' => $videos,
        );
    }
}
