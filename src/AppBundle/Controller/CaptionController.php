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
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
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
