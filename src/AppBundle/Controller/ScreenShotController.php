<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ScreenShot;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ScreenShot controller.
 *
 * @Route("/screen_shot")
 */
class ScreenShotController extends Controller {
    /**
     * Lists all ScreenShot entities.
     *
     * @param Request $request
     *
     * @return array
     * @Route("/", name="screen_shot_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(ScreenShot::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $screenShots = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'screenShots' => $screenShots,
        );
    }

    /**
     * Typeahead API endpoint for ScreenShot entities.
     * To make this work, add something like this to ScreenShotRepository:.
     *
     * @param Request $request
     * @Route("/typeahead", name="screen_shot_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse(array());
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(ScreenShot::class);
        $data = array();
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = array(
                'id' => $result->getId(),
                'text' => (string) $result,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * Search for ScreenShot entities.
     *
     * @param Request $request
     * @Route("/search", name="screen_shot_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:ScreenShot');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $screenShots = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $screenShots = array();
        }

        return array(
            'screenShots' => $screenShots,
            'q' => $q,
        );
    }

    /**
     * Finds and displays a ScreenShot entity.
     *
     * @param ScreenShot $screenShot
     *
     * @return array
     * @Route("/{id}", name="screen_shot_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(ScreenShot $screenShot) {
        return array(
            'screenShot' => $screenShot,
        );
    }
}
