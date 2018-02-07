<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Keyword;
use AppBundle\Form\KeywordType;

/**
 * Keyword controller.
 *
 * @Route("/keyword")
 * @Security("has_role('ROLE_USER')")
 */
class KeywordController extends Controller
{
    /**
     * Lists all Keyword entities.
     *
     * @Route("/", name="keyword_index")
     * @Method("GET")
     * @Template()
	 * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Keyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $keywords = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'keywords' => $keywords,
        );
    }

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}", name="keyword_show")
     * @Method("GET")
     * @Template()
	 * @param Keyword $keyword
     */
    public function showAction(Keyword $keyword)
    {

        return array(
            'keyword' => $keyword,
        );
    }

}
