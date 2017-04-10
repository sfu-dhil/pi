<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\MetadataField;
use AppBundle\Form\MetadataFieldType;

/**
 * MetadataField controller.
 *
 * @Route("/metadata_field")
 */
class MetadataFieldController extends Controller
{
    /**
     * Lists all MetadataField entities.
     *
     * @Route("/", name="metadata_field_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:MetadataField e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $metadataFields = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'metadataFields' => $metadataFields,
        );
    }
    /**
     * Search for MetadataField entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:MetadataField repository. Replace the fieldName with
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
     * @Route("/search", name="metadata_field_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:MetadataField');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$metadataFields = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$metadataFields = array();
		}

        return array(
            'metadataFields' => $metadataFields,
			'q' => $q,
        );
    }
    /**
     * Full text search for MetadataField entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:MetadataField repository. Replace the fieldName with
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
	 * fulltext indexes on your MetadataField entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="metadata_field_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:MetadataField');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$metadataFields = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$metadataFields = array();
		}

        return array(
            'metadataFields' => $metadataFields,
			'q' => $q,
        );
    }

    /**
     * Creates a new MetadataField entity.
     *
     * @Route("/new", name="metadata_field_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $metadataField = new MetadataField();
        $form = $this->createForm('AppBundle\Form\MetadataFieldType', $metadataField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($metadataField);
            $em->flush();

            $this->addFlash('success', 'The new metadataField was created.');
            return $this->redirectToRoute('metadata_field_show', array('id' => $metadataField->getId()));
        }

        return array(
            'metadataField' => $metadataField,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a MetadataField entity.
     *
     * @Route("/{id}", name="metadata_field_show")
     * @Method("GET")
     * @Template()
	 * @param MetadataField $metadataField
     */
    public function showAction(MetadataField $metadataField)
    {

        return array(
            'metadataField' => $metadataField,
        );
    }

    /**
     * Displays a form to edit an existing MetadataField entity.
     *
     * @Route("/{id}/edit", name="metadata_field_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param MetadataField $metadataField
     */
    public function editAction(Request $request, MetadataField $metadataField)
    {
        $editForm = $this->createForm('AppBundle\Form\MetadataFieldType', $metadataField);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The metadataField has been updated.');
            return $this->redirectToRoute('metadata_field_show', array('id' => $metadataField->getId()));
        }

        return array(
            'metadataField' => $metadataField,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a MetadataField entity.
     *
     * @Route("/{id}/delete", name="metadata_field_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param MetadataField $metadataField
     */
    public function deleteAction(Request $request, MetadataField $metadataField)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($metadataField);
        $em->flush();
        $this->addFlash('success', 'The metadataField was deleted.');

        return $this->redirectToRoute('metadata_field_index');
    }
}
