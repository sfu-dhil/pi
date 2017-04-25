<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Caption;
use AppBundle\Entity\ProfileElement;
use AppBundle\Entity\Video;
use AppBundle\Form\VideoProfileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Video controller.
 *
 * @Route("/video")
 */
class VideoController extends Controller {

    /**
     * Lists all Video entities.
     *
     * @Route("/", name="video_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Video e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $videos = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'videos' => $videos,
        );
    }

    /**
     * Search for Video entities.
     *
     * To make this work, add a method like this one to the 
     * AppBundle:Video repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     * 
      //    public function searchQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->where("e.fieldName like '%$q%'");
      //        return $qb->getQuery();
      //    }
     *
     *
     * @Route("/search", name="video_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Video');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->searchQuery($q);
            $paginator = $this->get('knp_paginator');
            $videos = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $videos = array();
        }

        return array(
            'videos' => $videos,
            'q' => $q,
        );
    }

    /**
     * Finds and displays a Video entity.
     *
     * @Route("/{id}", name="video_show")
     * @Method("GET")
     * @Template()
     * @param Video $video
     */
    public function showAction(Video $video) {

        return array(
            'video' => $video,
        );
    }
    
    /**
     * @Route("/{id}/refresh", name="video_refresh")
     * @Method("GET")
     * @param Video $video
     */
    public function refreshAction(Video $video) {
        $em = $this->getDoctrine()->getManager();
        $client = $this->get('yt.client');
        $client->updateVideos(array($video));
        $em->flush();
        $this->addFlash('success', 'The video data has been updated.');
        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }
    
    /**
     * @Route("/{id}/captions", name="video_captions")
     * @Method("GET")
     * @param Video $video
     */
    public function captionsAction(Video $video) {
        $oldCaptions = $video->getCaptions()->toArray();
        $em = $this->getDoctrine()->getManager();
        $captionRepo = $em->getRepository(Caption::class);
        $client = $this->get('yt.client');
        
        $captionIds = $client->captionIds($video);
        
        foreach($captionIds as $captionId) {
            $caption = $captionRepo->findOneBy(array('youtubeId' => $captionId));
            if( ! $caption) {
                $caption = new Caption();
                $caption->setYoutubeId($captionId);
                $em->persist($caption);
            }            
            $video->addCaption($caption);
            $caption->setVideo($video);
        }
        $em->flush();
        $this->addFlash('success', 'The video captions have been updated.');
        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }
    
    /**
     * @Route("/{id}/threads", name="video_threads")
     * @Method("GET")
     * @param Video $video
     */
    public function threadsAction(Video $video) {
        $client = $this->get('yt.client');
        $client->updateThreadIds($video);
        $this->addFlash('success', 'The video comment threads have been updated.');
        return $this->redirectToRoute('video_show', array('id' => $video->getId()));
    }
    
    /**
     * @Route("/{id}/profile", name="video_profile")
     * @Method({"GET","POST"})
     * @param Video $video
     * @Template()
     */
    public function profileAction(Request $request, Video $video) {
        $em = $this->getDoctrine()->getManager();
        $profileElements = $em->getRepository(ProfileElement::class)->findAll();
        $form = $this->createForm(VideoProfileType::class, null, array(
            'profile_elements' => $profileElements,
        ));
        return array(
            'video' => $video,
            'form' => $form->createView(),
        );
    }
    
}
