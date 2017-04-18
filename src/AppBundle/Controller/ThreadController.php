<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Thread;
use AppBundle\Form\ThreadType;

/**
 * Thread controller.
 *
 * @Route("/thread")
 */
class ThreadController extends Controller
{
    /**
     * Lists all Thread entities.
     *
     * @Route("/", name="thread_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Thread e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $threads = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'threads' => $threads,
        );
    }
    /**
     * Search for Thread entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Thread repository. Replace the fieldName with
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
     * @Route("/search", name="thread_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Thread');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$threads = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$threads = array();
		}

        return array(
            'threads' => $threads,
			'q' => $q,
        );
    }
    /**
     * Full text search for Thread entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Thread repository. Replace the fieldName with
	 * something appropriate, and adjust the generated fulltext.html.twig
	 * template.
	 * 
	//    public function fulltextQuery($q) {
	//        $qb = $this->createQueryBuilder('e');
	//        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
	//        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
	//        $qb->orderBy('score', 'desc');
	//        $qb->setParameter('q', $q);
	//        return $qb->getQuery();
	//    }	 
	 * 
	 * Requires a MatchAgainst function be added to doctrine, and appropriate
	 * fulltext indexes on your Thread entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="thread_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Thread');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$threads = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$threads = array();
		}

        return array(
            'threads' => $threads,
			'q' => $q,
        );
    }

    /**
     * Creates a new Thread entity.
     *
     * @Route("/new", name="thread_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $thread = new Thread();
        $form = $this->createForm('AppBundle\Form\ThreadType', $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($thread);
            $em->flush();

            $this->addFlash('success', 'The new thread was created.');
            return $this->redirectToRoute('thread_show', array('id' => $thread->getId()));
        }

        return array(
            'thread' => $thread,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Thread entity.
     *
     * @Route("/{id}", name="thread_show")
     * @Method("GET")
     * @Template()
	 * @param Thread $thread
     */
    public function showAction(Thread $thread)
    {

        return array(
            'thread' => $thread,
        );
    }

    /**
     * Displays a form to edit an existing Thread entity.
     *
     * @Route("/{id}/edit", name="thread_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param Thread $thread
     */
    public function editAction(Request $request, Thread $thread)
    {
        $editForm = $this->createForm('AppBundle\Form\ThreadType', $thread);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The thread has been updated.');
            return $this->redirectToRoute('thread_show', array('id' => $thread->getId()));
        }

        return array(
            'thread' => $thread,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Thread entity.
     *
     * @Route("/{id}/delete", name="thread_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param Thread $thread
     */
    public function deleteAction(Request $request, Thread $thread)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($thread);
        $em->flush();
        $this->addFlash('success', 'The thread was deleted.');

        return $this->redirectToRoute('thread_index');
    }
}
