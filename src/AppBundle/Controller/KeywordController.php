<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Keyword;
use AppBundle\Form\KeywordType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Keyword controller.
 *
 * @Route("/keyword")
 */
class KeywordController extends Controller {

    /**
     * Lists all Keyword entities.
     *
     * @Route("/", name="keyword_index", methods={"GET"})
     *
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
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
     * Generates a summary of the youtube keyword usage.
     *
     * @Route("/download", name="keyword_download", methods={"GET"})
     */
    public function downloadAction() {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Keyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $iterator = $query->iterate();
        $data = array();
        $data[0] = array(
            'id', 'keyword', 'count', 'url'
        );
        while($row = $iterator->next()) {
            /** @var Keyword $keyword */
            $keyword = $row[0];
            $data[] = [
                $keyword->getId(),
                $keyword->getLabel(),
                $keyword->getVideos()->count(),
                $this->generateUrl('keyword_show', array('id' => $keyword->getId()), UrlGeneratorInterface::ABSOLUTE_URL),
            ];
            $em->detach($keyword);
        }
        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, ['Content-Type' => 'text/csv']);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'keyword-usage.csv');
        $response->headers->set('Content-Disposition', $disposition);
        return $response;
    }

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}", name="keyword_show", methods={"GET"})
     *
     * @Template()
     * @param Keyword $keyword
     */
    public function showAction(Keyword $keyword) {

        return array(
            'keyword' => $keyword,
        );
    }
}
