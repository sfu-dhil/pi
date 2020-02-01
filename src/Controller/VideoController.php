<?php

namespace App\Controller;

use App\Entity\ProfileElement;
use App\Entity\ScreenShot;
use App\Entity\Video;
use App\Entity\VideoProfile;
use App\Form\ScreenShotType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Video controller.
 *
 * @Route("/video")
 */
class VideoController extends AbstractController {
    /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index", methods={"GET"})
     *
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request) {
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
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse(array());
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $data = array();
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = array(
                'id' => $result->getId(),
                'text' => (string) $result,
            );
        }

        return new JsonResponse($data);
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}", name="video_show", methods={"GET"})
     *
     * @Template()
     *
     * @param Video $video
     *
     * @return array
     */
    public function showAction(Video $video) {
        $user = $this->getUser();
        if (null === $user && $video->getHidden()) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ))
        ;
        if ( ! $videoProfile) {
            $videoProfile = new VideoProfile();
        }

        $elements = $em->getRepository(ProfileElement::class)->findAll();

        return array(
            'video' => $video,
            'elements' => $elements,
            'videoProfile' => $videoProfile,
        );
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}/keyword_download", name="video_keyword_download", methods={"GET"})
     *
     * @param Video $video
     *
     * @return Response
     */
    public function keywordDownloadAction(Video $video) {
        $data = array();
        $data[0] = array(
            'Keyword ID', 'Keyword', 'URL',
        );
        foreach($video->getKeywords() as $keyword) {
            $row = array(
                $keyword->getId(),
                $keyword->getName(),
                $this->generateUrl('keyword_show', array(
                    'id' => $keyword->getId()
                ), UrlGeneratorInterface::ABSOLUTE_URL),
            );

            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, array('Content-Type' => 'text/csv'));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'video-keywords-' . $video->getId() . '.csv'
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;

    }

    /**
     * Creates a new ScreenShot entity.
     *
     * @param Request $request
     * @param Video $video
     *
     * @return array|RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/new_screenshot", name="video_screen_shot_new", methods={"GET","POST"})
     *
     * @Template()
     */
    public function newScreenshotAction(Request $request, Video $video) {
        $screenShot = new ScreenShot();
        $screenShot->setVideo($video);
        $form = $this->createForm(ScreenShotType::class, $screenShot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($screenShot);
            $em->flush();

            $this->addFlash('success', 'The new screen shot was created.');

            return $this->redirectToRoute('video_show', array('id' => $video->getId()));
        }

        return array(
            'video' => $video,
            'screenShot' => $screenShot,
            'form' => $form->createView(),
        );
    }

    /**
     * Delete a screenshot.
     *
     * @param Request $request
     * @param Video $video
     * @param ScreenShot $screenShot
     *
     * @return RedirectResponse
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete_screenshot/{screenshotId}", name="video_screen_shot_delete", methods={"GET"})
     *
     * @Template()
     */
    public function deleteScreenshotAction(Request $request, Video $video, ScreenShot $screenShot) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($screenShot);
        $em->flush();

        $this->addFlash('success', 'The screenshot was removed.');

        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }
}
