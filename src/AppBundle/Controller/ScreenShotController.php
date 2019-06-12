<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ScreenShot;
use AppBundle\Form\ScreenShotType;

/**
 * ScreenShot controller.
 * @Security("has_role('ROLE_USER')")
 * @Route("/screen_shot")
 */
class ScreenShotController extends Controller
{
    /**
     * Lists all ScreenShot entities.
     *
     * @param Request $request
     *
     * @return array
     * @Route("/", name="screen_shot_index", methods={"GET"})
     *
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('e')->from(ScreenShot::class, 'e')->orderBy('e.id', 'ASC');
        $query = $qb->getQuery();
        $paginator = $this->get('knp_paginator');
        $screenShots = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'screenShots' => $screenShots,
        );
    }

    /**
     * Typeahead API endpoint for ScreenShot entities.
     * To make this work, add something like this to ScreenShotRepository:
     *
     * @param Request $request
     * @Route("/typeahead", name="screen_shot_typeahead", methods={"GET"})
     *
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request)
    {
        $q = $request->query->get('q');
        if (!$q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(ScreenShot::class);
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * Search for ScreenShot entities.
     *
     * @param Request $request
     * @Route("/search", name="screen_shot_search", methods={"GET"})
     *
     * @Template()
     * @return array
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:ScreenShot');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $screenShots = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $screenShots = array();
        }

        return array(
            'screenShots' => $screenShots,
            'q' => $q,
        );
    }

    /**
     * Creates a new ScreenShot entity.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new", name="screen_shot_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newAction(Request $request)
    {
        $screenShot = new ScreenShot();
        $form = $this->createForm(ScreenShotType::class, $screenShot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($screenShot);
            $em->flush();

            $this->addFlash('success', 'The new screenShot was created.');
            return $this->redirectToRoute('screen_shot_show', array('id' => $screenShot->getId()));
        }

        return array(
            'screenShot' => $screenShot,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new ScreenShot entity in a popup.
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/new_popup", name="screen_shot_new_popup", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newPopupAction(Request $request)
    {
        return $this->newAction($request);
    }

    /**
     * Finds and displays a ScreenShot entity.
     *
     * @param ScreenShot $screenShot
     *
     * @return array
     * @Route("/{id}", name="screen_shot_show", methods={"GET"})
     *
     * @Template()
     */
    public function showAction(ScreenShot $screenShot)
    {

        return array(
            'screenShot' => $screenShot,
        );
    }

    /**
     * Displays a form to edit an existing ScreenShot entity.
     *
     * @param Request $request
     * @param ScreenShot $screenShot
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/edit", name="screen_shot_edit", methods={"GET","POST"})
     *
     * @Template()
     */
    public function editAction(Request $request, ScreenShot $screenShot)
    {
        $editForm = $this->createForm(ScreenShotType::class, $screenShot);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $this->addFlash('success', 'The screenShot has been updated.');
            return $this->redirectToRoute('screen_shot_show', array('id' => $screenShot->getId()));
        }

        return array(
            'screenShot' => $screenShot,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * Deletes a ScreenShot entity.
     *
     * @param Request $request
     * @param ScreenShot $screenShot
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete", name="screen_shot_delete", methods={"GET"})
     *
     */
    public function deleteAction(Request $request, ScreenShot $screenShot)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($screenShot);
        $em->flush();
        $this->addFlash('success', 'The screenShot was deleted.');

        return $this->redirectToRoute('screen_shot_index');
    }
}
