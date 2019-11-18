<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Caption;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Caption controller.
 *
 * @Route("/caption")
 */
class CaptionController extends Controller {

    /**
     * Lists all Caption entities.
     *
     * @Route("/", name="caption_index")
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Caption::class);
        $query = $repo->findCaptionsQuery($this->getUser());
        $paginator = $this->get('knp_paginator');
        $captions = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'captions' => $captions,
        );
    }

    /**
     * Finds and displays a Caption entity.
     *
     * @Route("/{id}", name="caption_show")
     *
     * @Template()
     *
     * @param Caption $caption
     *
     * @return array
     */
    public function showAction(Caption $caption) {
        if (null === $this->getuser() && $caption->getVideo()->getHidden()) {
            throw new NotFoundHttpException();
        }

        return array(
            'caption' => $caption,
        );
    }
}
