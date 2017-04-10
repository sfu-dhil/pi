<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ProfileElement;
use AppBundle\Form\ProfileElementType;

/**
 * ProfileElement controller.
 *
 * @Route("/profile_element")
 */
class ProfileElementController extends Controller
{
    /**
     * Lists all ProfileElement entities.
     *
     * @Route("/", name="profile_element_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:ProfileElement e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $profileElements = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'profileElements' => $profileElements,
        );
    }
    /**
     * Search for ProfileElement entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileElement repository. Replace the fieldName with
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
     * @Route("/search", name="profile_element_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileElement');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileElements = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileElements = array();
		}

        return array(
            'profileElements' => $profileElements,
			'q' => $q,
        );
    }
    /**
     * Full text search for ProfileElement entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileElement repository. Replace the fieldName with
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
	 * fulltext indexes on your ProfileElement entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="profile_element_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileElement');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileElements = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileElements = array();
		}

        return array(
            'profileElements' => $profileElements,
			'q' => $q,
        );
    }

    /**
     * Creates a new ProfileElement entity.
     *
     * @Route("/new", name="profile_element_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $profileElement = new ProfileElement();
        $form = $this->createForm('AppBundle\Form\ProfileElementType', $profileElement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profileElement);
            $em->flush();

            $this->addFlash('success', 'The new profileElement was created.');
            return $this->redirectToRoute('profile_element_show', array('id' => $profileElement->getId()));
        }

        return array(
            'profileElement' => $profileElement,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProfileElement entity.
     *
     * @Route("/{id}", name="profile_element_show")
     * @Method("GET")
     * @Template()
	 * @param ProfileElement $profileElement
     */
    public function showAction(ProfileElement $profileElement)
    {

        return array(
            'profileElement' => $profileElement,
        );
    }

    /**
     * Displays a form to edit an existing ProfileElement entity.
     *
     * @Route("/{id}/edit", name="profile_element_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param ProfileElement $profileElement
     */
    public function editAction(Request $request, ProfileElement $profileElement)
    {
        $editForm = $this->createForm('AppBundle\Form\ProfileElementType', $profileElement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The profileElement has been updated.');
            return $this->redirectToRoute('profile_element_show', array('id' => $profileElement->getId()));
        }

        return array(
            'profileElement' => $profileElement,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ProfileElement entity.
     *
     * @Route("/{id}/delete", name="profile_element_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param ProfileElement $profileElement
     */
    public function deleteAction(Request $request, ProfileElement $profileElement)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($profileElement);
        $em->flush();
        $this->addFlash('success', 'The profileElement was deleted.');

        return $this->redirectToRoute('profile_element_index');
    }
}
