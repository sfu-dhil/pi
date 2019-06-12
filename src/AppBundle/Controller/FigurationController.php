<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Figuration;
use AppBundle\Form\FigurationType;

/**
 * Figuration controller.
 *
 * @Security("has_role('ROLE_USER')")
 * @Route("/figuration")
 */
class FigurationController extends Controller
{
    /**
     * Lists all Figuration entities.
     *
     * @param Request $request
     *
     * @return array
     *
     * @Route("/", name="figuration_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(Figuration::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $figurations = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'figurations' => $figurations,
        );
    }

    /**
     * Creates a new Figuration entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="figuration_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request)
    {
        $figuration = new Figuration();
        $form = $this->createForm(FigurationType::class, $figuration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($figuration);
            $em->flush();

            $this->addFlash('success', 'The new figuration was created.');
            return $this->redirectToRoute('figuration_show', array('id' => $figuration->getId()));
        }

        return array(
            'figuration' => $figuration,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Figuration entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="figuration_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a Figuration entity.
     *
     * @param Figuration $figuration
     *
     * @return array
     *
     * @Route("/{id}", name="figuration_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(Figuration $figuration)
    {

        return array(
            'figuration' => $figuration,
        );
    }

    /**
     * Displays a form to edit an existing Figuration entity.
     *
     *
     * @param Request $request
     * @param Figuration $figuration
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="figuration_edit", methods={"GET"})
     *
     * @Template()
     */
    public function editAction(Request $request, Figuration $figuration)
    {
        $editForm = $this->createForm(FigurationType::class, $figuration);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The figuration has been updated.');
            return $this->redirectToRoute('figuration_show', array('id' => $figuration->getId()));
        }

        return array(
            'figuration' => $figuration,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a Figuration entity.
     *
     *
     * @param Request $request
     * @param Figuration $figuration
     *
     * @return array|RedirectResponse
     *
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="figuration_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, Figuration $figuration)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($figuration);
        $em->flush();
        $this->addFlash('success', 'The figuration was deleted.');

        return $this->redirectToRoute('figuration_index');
    }
}
