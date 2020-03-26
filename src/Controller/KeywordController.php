<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Keyword;
use App\Entity\Video;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
class KeywordController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Keyword entities.
     *
     * @Route("/", name="keyword_index", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $dql = 'SELECT e FROM App:Keyword e ORDER BY e.id';
        $query = $em->createQuery($dql);

        $keywords = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'keywords' => $keywords,
        ];
    }

    /**
     * Generates a summary of the youtube keyword usage.
     *
     * @Route("/download", name="keyword_download", methods={"GET"})
     */
    public function downloadAction(EntityManagerInterface $em) {
        $repo = $em->getRepository(Video::class);
        $user = $this->getUser();

        $dql = 'SELECT e FROM App:Keyword e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $iterator = $query->iterate();
        $data = [];
        $data[0] = [
            'id', 'keyword', 'count', 'url',
        ];
        while ($row = $iterator->next()) {
            /** @var Keyword $keyword */
            $keyword = $row[0];
            $videos = $repo->findVideosQuery($user, [
                'type' => Keyword::class,
                'id' => $keyword->getId(),
            ])->execute();

            $data[] = [
                $keyword->getId(),
                $keyword->getLabel(),
                count($videos),
                $this->generateUrl('keyword_show', [
                    'id' => $keyword->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];
            $em->detach($keyword);
        }
        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, ['Content-Type' => 'text/csv']);
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
     * @return array
     */
    public function showAction(Request $request, Keyword $keyword) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), array(
            'type' => Keyword::class,
            'id' => $keyword->getId(),
        ));
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 20);
        return array(
            'keyword' => $keyword,
            'videos' => $videos,
        );
    }
    
    
  

    /**
     * Finds and displays a Keyword entity.
     *
     * @Route("/{id}/detail_download", name="keyword_details_download", methods={"GET"})
     *
     * @Template()
     *
     * @return Response
     */
    public function downloadDetailsAction(Keyword $keyword) {
        $data = [];
        $data[0] = [
            'Video Id', 'Video Title', 'Video URL', 'Figuration Id', 'Figuration', 'Figuration URL',
        ];
        foreach ($keyword->getVideos() as $video) {
            if ($video->getHidden() && ! $this->getUser()) {
                continue;
            }
            $figuration = $video->getFiguration();
            $row = [
                $video->getId(),
                $video->getTitle(),
                $this->generateUrl('video_show', [
                    'id' => $video->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                ($figuration ? $figuration->getId() : null),
                ($figuration ? $figuration->getName() : null),
                ($figuration ? $this->generateUrl('figuration_show', [
                    'id' => $figuration->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL) : null),
            ];
            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, ['Content-Type' => 'text/csv']);
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'keyword-details-' . $keyword->getId() . '.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
