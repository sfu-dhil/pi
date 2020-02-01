<?php

namespace App\Controller;

use App\Entity\ProfileElement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * ProfileElement controller.
 *
 * @Route("/profile_element")
 */
class ProfileElementController extends AbstractController {

    /**
     * Lists all ProfileElement entities.
     *
     * @Route("/", name="profile_element_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM App:ProfileElement e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $profileElements = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'profileElements' => $profileElements,
        );
    }

    /**
     * Finds and displays a ProfileElement entity.
     *
     * @Route("/{id}", name="profile_element_show", methods={"GET"})
     *
     * @Template()
     *
     * @param ProfileElement $profileElement
     *
     * @return array
     */
    public function showAction(ProfileElement $profileElement) {
        return array(
            'profileElement' => $profileElement,
        );
    }
}
