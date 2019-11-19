<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Keyword;
use AppBundle\Entity\Video;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
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
     *
     * @param Request $request
     *
     * @return array
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
        $repo = $em->getRepository(Video::class);
        $user = $this->getUser();

        $dql = 'SELECT e FROM AppBundle:Keyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $iterator = $query->iterate();
        $data = array();
        $data[0] = array(
            'id', 'keyword', 'count', 'url',
        );
        while ($row = $iterator->next()) {
            /** @var Keyword $keyword */
            $keyword = $row[0];
            $videos = $repo->findVideosQuery($user, array(
                'type' => Keyword::class,
                'id' => $keyword->getId(),
            ))->execute();

            $data[] = array(
                $keyword->getId(),
                $keyword->getLabel(),
                count($videos),
                $this->generateUrl('keyword_show', array(
                    'id' => $keyword->getId(),
                ), UrlGeneratorInterface::ABSOLUTE_URL),
            );
            $em->detach($keyword);
        }
        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, array('Content-Type' => 'text/csv'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'keyword-usage.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}", name="keyword_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Keyword $keyword
     *
     * @return array
     */
    public function showAction(Keyword $keyword) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), array(
            'type' => Keyword::class,
            'id' => $keyword->getId(),
        ));

        return array(
            'keyword' => $keyword,
            'videos' => $query->execute(),
        );
    }

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}/detail_download", name="keyword_details_download", methods={"GET"})
     *
     * @Template()
     *
     * @param Keyword $keyword
     *
     * @return Response
     */
    public function downloadDetailsAction(Keyword $keyword) {
        $data = array();
        $data[0] = array(
            'Video Id', 'Video Title', 'Video URL', 'Figuration Id', 'Figuration', 'Figuration URL'
        );
        foreach($keyword->getVideos() as $video) {
            if ($video->getHidden() && ! $this->getUser()) {
                continue;
            }
            $figuration = $video->getFiguration();
            $row = array(
                $video->getId(),
                $video->getTitle(),
                $this->generateUrl('video_show', array(
                    'id' => $video->getId()
                ), UrlGeneratorInterface::ABSOLUTE_URL),
                ($figuration ? $figuration->getId() : null),
                ($figuration ? $figuration->getName() : null),
                ($figuration ? $this->generateUrl('figuration_show', array(
                    'id' => $figuration->getId()
                ), UrlGeneratorInterface::ABSOLUTE_URL) : null),
            );
            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, array('Content-Type' => 'text/csv'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'keyword-details-' . $keyword->getId() . '.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
