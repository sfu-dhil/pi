<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Caption;
use AppBundle\Form\CaptionType;

/**
 * Caption controller.
 *
 * @Route("/caption")
 */
class CaptionController extends Controller
{
    /**
     * Lists all Caption entities.
     *
     * @Route("/", name="caption_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Caption e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $captions = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'captions' => $captions,
        );
    }
    /**
     * Search for Caption entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Caption repository. Replace the fieldName with
	 * something appropriate, and adjust the generated search.html.twig
	 * template.
	 * 
     //    public function searchQuery($q) {
     //        $qb = $this->createQueryBuilder('e');
     //        $qb->where("e.fieldName like '%$q%'");
     //        return $qb->getQuery();
     //    }
	 *
     *
     * @Route("/search", name="caption_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Caption');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$captions = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$captions = array();
		}

        return array(
            'captions' => $captions,
			'q' => $q,
        );
    }

    /**
     * Finds and displays a Caption entity.
     *
     * @Route("/{id}", name="caption_show")
     * @Method("GET")
     * @Template()
	 * @param Caption $caption
     */
    public function showAction(Caption $caption)
    {

        return array(
            'caption' => $caption,
        );
    }
    
    /**
     * @Route("/{id}/refresh", name="caption_refresh")
     * @param Caption $caption
     */
    public function refreshAction(Caption $caption) {
        $em = $this->getDoctrine()->getManager();
        $client = $this->get('yt.client');
        $client->updateCaption($caption);
        $em->flush();
        $this->addFlash('success', 'The video caption has been updated.');
        return $this->redirectToRoute('caption_show', array(
            'id' => $caption->getId(),
        ));
    }
}
