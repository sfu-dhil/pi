<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\MetadataElement;
use AppBundle\Form\MetadataElementType;

/**
 * MetadataElement controller.
 *
 * @Route("/metadata_element")
 */
class MetadataElementController extends Controller
{
    /**
     * Lists all MetadataElement entities.
     *
     * @Route("/", name="metadata_element_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:MetadataElement e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $metadataElements = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'metadataElements' => $metadataElements,
        );
    }
    /**
     * Search for MetadataElement entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:MetadataElement repository. Replace the fieldName with
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
     * @Route("/search", name="metadata_element_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:MetadataElement');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$metadataElements = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$metadataElements = array();
		}

        return array(
            'metadataElements' => $metadataElements,
			'q' => $q,
        );
    }
    /**
     * Full text search for MetadataElement entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:MetadataElement repository. Replace the fieldName with
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
	 * fulltext indexes on your MetadataElement entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="metadata_element_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:MetadataElement');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$metadataElements = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$metadataElements = array();
		}

        return array(
            'metadataElements' => $metadataElements,
			'q' => $q,
        );
    }

    /**
     * Creates a new MetadataElement entity.
     *
     * @Route("/new", name="metadata_element_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $metadataElement = new MetadataElement();
        $form = $this->createForm('AppBundle\Form\MetadataElementType', $metadataElement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($metadataElement);
            $em->flush();

            $this->addFlash('success', 'The new metadataElement was created.');
            return $this->redirectToRoute('metadata_element_show', array('id' => $metadataElement->getId()));
        }

        return array(
            'metadataElement' => $metadataElement,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a MetadataElement entity.
     *
     * @Route("/{id}", name="metadata_element_show")
     * @Method("GET")
     * @Template()
	 * @param MetadataElement $metadataElement
     */
    public function showAction(MetadataElement $metadataElement)
    {

        return array(
            'metadataElement' => $metadataElement,
        );
    }

    /**
     * Displays a form to edit an existing MetadataElement entity.
     *
     * @Route("/{id}/edit", name="metadata_element_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param MetadataElement $metadataElement
     */
    public function editAction(Request $request, MetadataElement $metadataElement)
    {
        $editForm = $this->createForm('AppBundle\Form\MetadataElementType', $metadataElement);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The metadataElement has been updated.');
            return $this->redirectToRoute('metadata_element_show', array('id' => $metadataElement->getId()));
        }

        return array(
            'metadataElement' => $metadataElement,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a MetadataElement entity.
     *
     * @Route("/{id}/delete", name="metadata_element_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param MetadataElement $metadataElement
     */
    public function deleteAction(Request $request, MetadataElement $metadataElement)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($metadataElement);
        $em->flush();
        $this->addFlash('success', 'The metadataElement was deleted.');

        return $this->redirectToRoute('metadata_element_index');
    }
}
