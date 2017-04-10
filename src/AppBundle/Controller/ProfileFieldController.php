<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ProfileField;
use AppBundle\Form\ProfileFieldType;

/**
 * ProfileField controller.
 *
 * @Route("/profile_field")
 */
class ProfileFieldController extends Controller
{
    /**
     * Lists all ProfileField entities.
     *
     * @Route("/", name="profile_field_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:ProfileField e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $profileFields = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'profileFields' => $profileFields,
        );
    }
    /**
     * Search for ProfileField entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileField repository. Replace the fieldName with
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
     * @Route("/search", name="profile_field_search")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileField');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->searchQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileFields = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileFields = array();
		}

        return array(
            'profileFields' => $profileFields,
			'q' => $q,
        );
    }
    /**
     * Full text search for ProfileField entities.
	 *
	 * To make this work, add a method like this one to the 
	 * AppBundle:ProfileField repository. Replace the fieldName with
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
	 * fulltext indexes on your ProfileField entity.
	 *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
	 *
     *
     * @Route("/fulltext", name="profile_field_fulltext")
     * @Method("GET")
     * @Template()
	 * @param Request $request
	 * @return array
     */
    public function fulltextAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
		$repo = $em->getRepository('AppBundle:ProfileField');
		$q = $request->query->get('q');
		if($q) {
	        $query = $repo->fulltextQuery($q);
			$paginator = $this->get('knp_paginator');
			$profileFields = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
		} else {
			$profileFields = array();
		}

        return array(
            'profileFields' => $profileFields,
			'q' => $q,
        );
    }

    /**
     * Creates a new ProfileField entity.
     *
     * @Route("/new", name="profile_field_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        $profileField = new ProfileField();
        $form = $this->createForm('AppBundle\Form\ProfileFieldType', $profileField);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profileField);
            $em->flush();

            $this->addFlash('success', 'The new profileField was created.');
            return $this->redirectToRoute('profile_field_show', array('id' => $profileField->getId()));
        }

        return array(
            'profileField' => $profileField,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a ProfileField entity.
     *
     * @Route("/{id}", name="profile_field_show")
     * @Method("GET")
     * @Template()
	 * @param ProfileField $profileField
     */
    public function showAction(ProfileField $profileField)
    {

        return array(
            'profileField' => $profileField,
        );
    }

    /**
     * Displays a form to edit an existing ProfileField entity.
     *
     * @Route("/{id}/edit", name="profile_field_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
	 * @param ProfileField $profileField
     */
    public function editAction(Request $request, ProfileField $profileField)
    {
        $editForm = $this->createForm('AppBundle\Form\ProfileFieldType', $profileField);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The profileField has been updated.');
            return $this->redirectToRoute('profile_field_show', array('id' => $profileField->getId()));
        }

        return array(
            'profileField' => $profileField,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ProfileField entity.
     *
     * @Route("/{id}/delete", name="profile_field_delete")
     * @Method("GET")
	 * @param Request $request
	 * @param ProfileField $profileField
     */
    public function deleteAction(Request $request, ProfileField $profileField)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($profileField);
        $em->flush();
        $this->addFlash('success', 'The profileField was deleted.');

        return $this->redirectToRoute('profile_field_index');
    }
}
