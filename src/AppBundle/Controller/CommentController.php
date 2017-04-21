<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;

/**
 * Comment controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all Comment entities.
     *
     * @Route("/", name="comment_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Comment e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $comments = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'comments' => $comments,
        );
    }
    /**
     * Search for Comment entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Comment repository. Replace the fieldName with
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
     * @Route("/search", name="comment_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Comment');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$comments = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$comments = array();
		}

        return array(
            'comments' => $comments,
			'q' => $q,
        );
    }
    /**
     * Full text search for Comment entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:Comment repository. Replace the fieldName with
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
	 * fulltext indexes on your Comment entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="comment_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:Comment');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$comments = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$comments = array();
		}

        return array(
            'comments' => $comments,
			'q' => $q,
        );
    }

    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}", name="comment_show")
     * @Method("GET")
     * @Template()
	 * @param Comment $comment
     */
    public function showAction(Comment $comment)
    {

        return array(
            'comment' => $comment,
        );
    }

    /**
     * Finds and displays a Comment entity.
     *
     * @Route("/{id}/refresh", name="comment_refresh")
     * @Method("GET")
	 * @param Comment $comment
     */
    public function refreshAction(Comment $comment)
    {
        $client = $this->container->get('yt.client');
        $client->updateComments($comment->getThread());
        $this->addFlash('success', 'The comment has been refreshed.');
        return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
    }

}
