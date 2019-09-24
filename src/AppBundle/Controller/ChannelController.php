<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Video;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Channel;
use AppBundle\Form\ChannelType;
use AppBundle\Services\YoutubeClient;

/**
 * Channel controller.
 *
 * @Route("/channel")
 */
class ChannelController extends Controller {

    /**
     * Lists all Channel entities.
     *
     * @Route("/", name="channel_index", methods={"GET"})
     *
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Channel e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $channels = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'channels' => $channels,
        );
    }

    /**
     * Finds and displays a Channel entity.
     *
     * @Route("/{id}", name="channel_show", methods={"GET"})
     *
     * @Template()
     * @param Channel $channel
     */
    public function showAction(Channel $channel) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), array(
            'type' => Channel::class,
            'id' => $channel->getId(),
        ));
        return array(
            'channel' => $channel,
            'videos' => $query->execute(),
        );
    }

}
