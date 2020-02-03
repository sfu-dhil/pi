<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\ProfileElement;
use App\Entity\ScreenShot;
use App\Entity\Video;
use App\Entity\VideoProfile;
use App\Form\ScreenShotType;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
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
class VideoController extends AbstractController  implements PaginatorAwareInterface {
    use PaginatorTrait;
    /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index", methods={"GET"})
     *
     * @Template()
     *
     * @return array
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser());

        $videos = $this->paginator->paginate($query, $request->query->getint('page', 1), 25, [
            'defaultSortFieldName' => 'e.id',
            'defaultSortDirection' => 'asc',
        ]);

        return [
            'videos' => $videos,
        ];
    }

    /**
     * Typeahead API endpoint for ScreenShot entities.
     *
     * @Route("/typeahead", name="video_typeahead", methods={"GET"})
     *
     * @return JsonResponse
     */
    public function typeahead(Request $request) {
        $q = $request->query->get('q');
        if ( ! $q) {
            return new JsonResponse([]);
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $data = [];
        foreach ($repo->typeaheadQuery($q) as $result) {
            $data[] = [
                'id' => $result->getId(),
                'text' => (string) $result,
            ];
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
     * @return array
     */
    public function showAction(Video $video) {
        $user = $this->getUser();
        if (null === $user && $video->getHidden()) {
            throw new NotFoundHttpException();
        }

        $em = $this->getDoctrine()->getManager();
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy([
            'user' => $user,
            'video' => $video,
        ])
        ;
        if ( ! $videoProfile) {
            $videoProfile = new VideoProfile();
        }

        $elements = $em->getRepository(ProfileElement::class)->findAll();

        return [
            'video' => $video,
            'elements' => $elements,
            'videoProfile' => $videoProfile,
        ];
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}/keyword_download", name="video_keyword_download", methods={"GET"})
     *
     * @return Response
     */
    public function keywordDownloadAction(Video $video) {
        $data = [];
        $data[0] = [
            'Keyword ID', 'Keyword', 'URL',
        ];
        foreach ($video->getKeywords() as $keyword) {
            $row = [
                $keyword->getId(),
                $keyword->getName(),
                $this->generateUrl('keyword_show', [
                    'id' => $keyword->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
            ];

            $data[] = $row;
        }

        $csv = $this->container->get('serializer')->encode($data, 'csv');
        $response = new Response($csv, 200, ['Content-Type' => 'text/csv']);
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
     * @return array|RedirectResponse
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
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

            return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
        }

        return [
            'video' => $video,
            'screenShot' => $screenShot,
            'form' => $form->createView(),
        ];
    }

    /**
     * Delete a screenshot.
     *
     * @return RedirectResponse
     * @Security("is_granted('ROLE_CONTENT_ADMIN')")
     * @Route("/{id}/delete_screenshot/{screenshotId}", name="video_screen_shot_delete", methods={"GET"})
     *
     * @Template()
     */
    public function deleteScreenshotAction(Request $request, Video $video, ScreenShot $screenShot) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($screenShot);
        $em->flush();

        $this->addFlash('success', 'The screenshot was removed.');

        return $this->redirectToRoute('video_show', ['id' => $video->getId()]);
    }
}
