<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Video;
use AppBundle\Form\VideoType;

/**
 * Video controller.
 *
 * @Route("/video")
 */
class VideoController extends Controller
{
    /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Video e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'videos' => $videos,
        );
    }
    /**
     * Search for Video entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Video repository. Replace the fieldName with
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
     * @Route("/search", name="video_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Video');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$videos = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$videos = array();
		}

        return array(
            'videos' => $videos,
			'q' => $q,
        );
    }

    /**
     * Creates a new Video entity.
     *
     * @Route("/new", name="video_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $video = new Video();
        $form = $this->createForm('AppBundle\Form\VideoType', $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service = $this->get('yt.video');
            $service->update($video);
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($video);
//            $em->flush();
//
            $this->addFlash('success', 'The new video was created.');
//            return $this->redirectToRoute('video_show', array('id' => $video->getId()));
        }

        return array(
            'video' => $video,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}", name="video_show")
     * @Method("GET")
     * @Template()
	 * @param Video $video
     */
    public function showAction(Video $video)
    {

        return array(
            'video' => $video,
        );
    }

    /**
     * Displays a form to edit an existing Video entity.
     *
     * @Route("/{id}/edit", name="video_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Video $video
     */
    public function editAction(Request $request, Video $video)
    {
        $editForm = $this->createForm('AppBundle\Form\VideoType', $video);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The video has been updated.');
            return $this->redirectToRoute('video_show', array('id' => $video->getId()));
        }

        return array(
            'video' => $video,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Video entity.
     *
     * @Route("/{id}/delete", name="video_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Video $video
     */
    public function deleteAction(Request $request, Video $video)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();
        $this->addFlash('success', 'The video was deleted.');

        return $this->redirectToRoute('video_index');
    }
}
