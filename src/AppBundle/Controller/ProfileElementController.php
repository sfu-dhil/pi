<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ProfileElement;
use AppBundle\Form\ProfileElementType;

/**
 * ProfileElement controller.
 *
 * @Route("/profile_element")
 */
class ProfileElementController extends Controller {

    /**
     * Lists all ProfileElement entities.
     *
     * @Route("/", name="profile_element_index", methods={"GET"})
     *
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
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
     * Finds and displays a ProfileElement entity.
     *
     * @Route("/{id}", name="profile_element_show", methods={"GET"})
     *
     * @Template()
     * @param ProfileElement $profileElement
     */
    public function showAction(ProfileElement $profileElement) {

        return array(
            'profileElement' => $profileElement,
        );
    }

}
