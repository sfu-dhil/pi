<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\ScreenShot;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
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
class ScreenShotController extends AbstractController  implements PaginatorAwareInterface {
    use PaginatorTrait;
    /**
     * Lists all ScreenShot entities.
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

        $screenShots = $this->paginator->paginate($query, $request->query->getint('page', 1), 25);

        return [
            'screenShots' => $screenShots,
        ];
    }

    /**
     * Typeahead API endpoint for ScreenShot entities.
     * To make this work, add something like this to ScreenShotRepository:.
     *
     * @Route("/typeahead", name="screen_shot_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(ScreenShot::class);
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
        }

        return new JsonResponse($data);
    }

    /**
     * Search for ScreenShot entities.
     *
     * @Route("/search", name="screen_shot_search", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('App:ScreenShot');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);

            $screenShots = $this->paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $screenShots = [];
        }

        return [
            'screenShots' => $screenShots,
            'q' => $q,
        ];
    }

    /**
     * Finds and displays a ScreenShot entity.
     *
     * @return array
     * @Route("/{id}", name="screen_shot_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(ScreenShot $screenShot) {
        return [
            'screenShot' => $screenShot,
        ];
    }
}
