<?php

namespace App\Controller;

use App\Entity\ProfileElement;
use App\Entity\Video;
use App\Entity\VideoProfile;
use Closure;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Nines\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * VideoProfile controller.
 *
 * @Route("/video_profile")
 */
class VideoProfileController extends AbstractController {

    /**
     * Lists all VideoProfile entities.
     *
     * @Route("/", name="video_profile_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $dql = 'SELECT e FROM App:VideoProfile e WHERE e.user = :user ORDER BY e.id';
        $query = $em->createQuery($dql);
        $query->setParameter('user', $user);
        $paginator = $this->get('knp_paginator');

        $videoProfiles = $paginator->paginate($query, $request->query->getint('page', 1), 25);
        $userSummary = $em->getRepository(VideoProfile::class)->userSummary();
        $videoSummary = $em->getRepository(VideoProfile::class)->videoSummary();

        return array(
            'videoProfiles' => $videoProfiles,
            'userSummary' => $userSummary,
            'videoSummary' => $videoSummary,
        );
    }

    /**
     * Convert a collection to an array and sort it.
     *
     * @param Collection $collection
     * @param Closure $callback
     *
     * @return array
     */
    public function collection2array(Collection $collection, Closure $callback = null) {
        if (null === $callback) {
            $array = $collection->map(function ($item) {
                return (string) $item;
            })->toArray();
        } else {
            $array = $collection->map($callback)->toArray();
        }
        sort($array);

        return $array;
    }

    /**
     * Download the video keywords.
     *
     * @Route("/download/keywords", name="video_profile_keywords_download", methods={"GET"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function keywordDownloadAction(Request $request, EntityManagerInterface $em) {
        $videos = $em->getRepository(Video::class)->findVideosQuery($this->getUser())->execute();
        $data = array();
        $data[0] = array('video id', 'URL', 'title', 'youtube keyword');
        foreach ($videos as $video) {
            $row = array();
            $row[0] = $video->getId();
            $row[] = $this->generateUrl('video_show', array('id' => $video->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
            $row[] = $video->getTitle();
            foreach ($video->getKeywords() as $keyword) {
                $row[] = $keyword->getName();
            }
            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, array('Content-Type' => 'text/csv'));
        $filename = 'youtube-keywords.csv';
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Download the video profiles for one user.
     *
     * @Route("/download/{userId}", name="video_profile_download", methods={"GET","POST"})
     * @ParamConverter("user", options={"id"="userId"})
     *
     * @param Request $request
     * @param User $user
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function downloadAction(Request $request, User $user, EntityManagerInterface $em) {
        $videos = $em->getRepository(Video::class)->findVideosQuery($this->getUser())->execute();
        $elements = $em->getRepository(ProfileElement::class)->findBy(array(), array('id' => 'ASC'));
        $data = array();
        $data[0] = array('video id', 'playlist', 'user id');
        foreach ($elements as $element) {
            $data[0][] = $element->getLabel();
        }
        $data[0][] = 'Url';
        $data[0][] = 'Title';

        foreach ($videos as $video) {
            $playlists = $this->collection2array($video->getPlaylists());
            $row = array($video->getId(), implode(', ', $playlists), ($this->getUser() ? $user->getUsername() : 'user'));
            $profile = $video->getVideoProfile($user);
            foreach ($elements as $element) {
                if ($profile) {
                    $keywords = $this->collection2array($profile->getProfileKeywords($element));
                    $row[] = implode(', ', $keywords);
                } else {
                    $row[] = '';
                }
            }
            $row[] = $this->generateUrl('video_show', array('id' => $video->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
            $row[] = $video->getTitle();
            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, array('Content-Type' => 'text/csv'));
        $filename = 'profiles.csv';
        if ($this->isGranted('ROLE_USER')) {
            $filename = $user->getUsername() . '.csv';
        }
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * Finds and displays a VideoProfile entity.
     *
     * @Route("/{videoId}", name="video_profile_show", methods={"GET"})
     *
     * @Template()
     *
     * @param mixed $videoId
     *
     * @return array
     */
    public function showAction($videoId) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->find(Video::class, $videoId);
        if ( ! $video) {
            throw new BadRequestHttpException('There is no video with that ID.');
        }
        $user = $this->getUser();
        if ( ! $user && $video->getHidden()) {
            throw new NotFoundHttpException('The requested video does not exist.');
        }
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ));
        if ( ! $videoProfile) {
            $videoProfile = new VideoProfile();
        }

        $elements = $em->getRepository(ProfileElement::class)->findAll();

        return array(
            'video' => $video,
            'videoProfile' => $videoProfile,
            'elements' => $elements,
        );
    }
}
