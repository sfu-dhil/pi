<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Caption;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\ProfileElement;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoProfile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\YoutubeClient;

/**
 * Video controller.
 * @Route("/video")
 */
class VideoController extends Controller
{

    /**
     * Lists all Video entities.
     * @Route("/", name="video_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser());
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 25, array(
            'defaultSortFieldName' => 'e.id',
            'defaultSortDirection' => 'asc',
        ));

        return array(
            'videos' => $videos,
        );
    }

    /**
     * Typeahead API endpoint for ScreenShot entities.
     *
     * @param Request $request
     *
     * @Route("/typeahead", name="video_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request)
    {
        $q = $request->query->get('q');
        if( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $data = [];
        foreach($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string)$result,
            ];
        }
        return new JsonResponse($data);
    }

    /**
     * Finds and displays a Video entity.
     * @Route("/{id}", name="video_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Video $video
     */
    public function showAction(Video $video)
    {
        $user = $this->getUser();
        if($user === null && $video->getHidden()) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ));
        if (!$videoProfile) {
            $videoProfile = new VideoProfile();
        }

        $elements = $em->getRepository(ProfileElement::class)->findAll();

        return array(
            'video' => $video,
            'elements' => $elements,
            'videoProfile' => $videoProfile,
        );
    }

}
