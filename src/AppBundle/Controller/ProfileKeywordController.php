<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ProfileKeyword;
use AppBundle\Form\ProfileKeywordType;

/**
 * ProfileKeyword controller.
 *
 * @Route("/profile_keyword")
 */
class ProfileKeywordController extends Controller
{
    /**
     * Lists all ProfileKeyword entities.
     *
     * @Route("/", name="profile_keyword_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:ProfileKeyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $profileKeywords = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'profileKeywords' => $profileKeywords,
        );
    }
    /**
     * Search for ProfileKeyword entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileKeyword repository. Replace the fieldName with
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
     * @Route("/search", name="profile_keyword_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileKeyword');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileKeywords = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileKeywords = array();
		}

        return array(
            'profileKeywords' => $profileKeywords,
			'q' => $q,
        );
    }
    /**
     * Full text search for ProfileKeyword entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileKeyword repository. Replace the fieldName with
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
	 * fulltext indexes on your ProfileKeyword entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="profile_keyword_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileKeyword');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileKeywords = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileKeywords = array();
		}

        return array(
            'profileKeywords' => $profileKeywords,
			'q' => $q,
        );
    }

    /**
     * Creates a new ProfileKeyword entity.
     *
     * @Route("/new", name="profile_keyword_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $profileKeyword = new ProfileKeyword();
        $form = $this->createForm('AppBundle\Form\ProfileKeywordType', $profileKeyword);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profileKeyword);
            $em->flush();

            $this->addFlash('success', 'The new profileKeyword was created.');
            return $this->redirectToRoute('profile_keyword_show', array('id' => $profileKeyword->getId()));
        }

        return array(
            'profileKeyword' => $profileKeyword,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProfileKeyword entity.
     *
     * @Route("/{id}", name="profile_keyword_show")
     * @Method("GET")
     * @Template()
	 * @param ProfileKeyword $profileKeyword
     */
    public function showAction(ProfileKeyword $profileKeyword)
    {

        return array(
            'profileKeyword' => $profileKeyword,
        );
    }

    /**
     * Displays a form to edit an existing ProfileKeyword entity.
     *
     * @Route("/{id}/edit", name="profile_keyword_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param ProfileKeyword $profileKeyword
     */
    public function editAction(Request $request, ProfileKeyword $profileKeyword)
    {
        $editForm = $this->createForm('AppBundle\Form\ProfileKeywordType', $profileKeyword);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The profileKeyword has been updated.');
            return $this->redirectToRoute('profile_keyword_show', array('id' => $profileKeyword->getId()));
        }

        return array(
            'profileKeyword' => $profileKeyword,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ProfileKeyword entity.
     *
     * @Route("/{id}/delete", name="profile_keyword_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param ProfileKeyword $profileKeyword
     */
    public function deleteAction(Request $request, ProfileKeyword $profileKeyword)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($profileKeyword);
        $em->flush();
        $this->addFlash('success', 'The profileKeyword was deleted.');

        return $this->redirectToRoute('profile_keyword_index');
    }
}
