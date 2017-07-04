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
     * Creates a new ProfileElement entity.
     *
     * @Route("/new", name="profile_element_new")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function newAction(Request $request)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
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
     * Creates a new ProfileElement entity.
     *
     * @Route("/{id}/edit", name="profile_element_edit")
     * @Method({"GET", "POST"})
     * @Template()
	 * @param Request $request
     */
    public function editAction(Request $request, ProfileElement $profileElement)
    {
        if( ! $this->isGranted('ROLE_CONTENT_ADMIN')) {
            $this->addFlash('danger', 'You must login to access this page.');
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
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
            'edit_form' => $form->createView(),
        );
    }


}
