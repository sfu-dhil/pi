<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProfileElement;
use AppBundle\Entity\ProfileKeyword;
use AppBundle\Entity\Video;
use AppBundle\Entity\VideoProfile;
use AppBundle\Form\VideoProfileType;
use Doctrine\Common\Collections\ArrayCollection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * VideoProfile controller.
 *
 * @Route("/video_profile")
 */
class VideoProfileController extends Controller {

    /**
     * Lists all VideoProfile entities.
     *
     * @Route("/", name="video_profile_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:VideoProfile e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $videoProfiles = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'videoProfiles' => $videoProfiles,
        );
    }

    /**
     * Finds and displays a VideoProfile entity.
     *
     * @Route("/{videoId}", name="video_profile_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($videoId) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->find(Video::class, $videoId);
        if (!$video) {
            throw new BadRequestHttpException("There is no video with that ID.");
        }
        $user = $this->getUser();
        $videoProfile = $em->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ));

        $elements = $em->getRepository(ProfileElement::class)->findAll();
        return array(
            'video' => $video,
            'videoProfile' => $videoProfile,
            'elements' => $elements,
        );
    }

    /**
     * @Route("/{videoId}/edit", name="video_profile_edit")
     * @Method({"GET","POST"})
     * @param Video $video
     * @Template()
     */
    public function editAction(Request $request, $videoId) {
        $em = $this->getDoctrine()->getManager();
        $video = $em->find(Video::class, $videoId);
        if (!$video) {
            throw new BadRequestHttpException("There is no video with that ID.");
        }
        $user = $this->getUser();
        $videoProfile = $this->getDoctrine()->getRepository(VideoProfile::class)->findOneBy(array(
            'user' => $user,
            'video' => $video,
        ));
        if (!$videoProfile) {
            $videoProfile = new VideoProfile();
            $videoProfile->setUser($user);
            $videoProfile->setVideo($video);
        }
        $profileElements = $em->getRepository(ProfileElement::class)->findAll();
        $form = $this->createForm(VideoProfileType::class, null, array(
            'profile_elements' => $profileElements,
            'profile' => $videoProfile,
        ));
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if (!$em->contains($videoProfile)) {
                $em->persist($videoProfile);
            }
            $profileKeywords = new ArrayCollection();
            foreach ($profileElements as $element) {
                $keywords = $form->get($element->getName())->getData();
                foreach ($keywords as $profileKeyword) {
                    $profileKeywords->add($profileKeyword);
                }
            }
            $videoProfile->setProfileKeywords($profileKeywords);
            $em->flush();
            $this->addFlash('success', 'The profile has been updated.');
            return $this->redirectToRoute('video_profile_show', array(
                'videoId' => $video->getId(),
            ));
        }
        return array(
            'video' => $video,
            'videoProfile' => $videoProfile,
            'edit_form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/selection", name="video_profile_selection")
     * @Method("GET")
     * @param Request $request
     */
    public function keywordSelectedAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $data = $request->query->get('data');

        $elementName = $request->query->get('name');
        $profileElement = $em->getRepository(ProfileElement::class)->findOneBy(array(
            'name' => $elementName,
        ));
        if (!$profileElement) {
            return new JsonResponse(array(
                'message' => 'No such profile element.',
            ));
        }

        $keywordName = $data['id'];
        $profileKeyword = $em->getRepository(ProfileKeyword::class)->findOneBy(array(
            'profileElement' => $profileElement,
            'name' => $keywordName,
        ));
        if (!$profileKeyword) {
            $profileKeyword = new ProfileKeyword();
            $profileKeyword->setProfileElement($profileElement);
            $profileKeyword->setName($keywordName);
            $profileKeyword->setLabel($data['text']);
            $em->persist($profileKeyword);
            $em->flush($profileKeyword);
            return new JsonResponse(array(
                'message' => "created keyword {$profileElement->getName()}:{$profileKeyword->getName()}",
            ));
        }

        return new JsonResponse(array(
            'message' => 'No action required',
        ));
    }

}
