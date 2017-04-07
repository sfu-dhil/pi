<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Channel;
use AppBundle\Form\ChannelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Channel controller.
 *
 * @Route("/channel")
 */
class ChannelController extends Controller {

    /**
     * Lists all Channel entities.
     *
     * @Route("/", name="channel_index")
     * @Method("GET")
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
     * Create a new Channel and get the metadata from Youtube.
     * 
     * @Route("/new", name="channel_new")
     * @Method({"GET","POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $channel = new Channel();
        $form = $this->createForm(ChannelType::class, $channel);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Created a new channel.');
            $service = $this->get('yt.channel');
            $service->update($channel);
            $em = $this->getDoctrine()->getManager();
            $em->persist($channel);
            $em->flush();
            return $this->redirect($this->generateUrl('channel_index'));
        }
        return array(
            'form' => $form->createView(),
            'channel' => $channel,
        );
    }    
    
    /**
     * Full text search for Channel entities.
     *
     * To make this work, add a method like this one to the 
     * AppBundle:Channel repository. Replace the fieldName with
     * something appropriate, and adjust the generated fulltext.html.twig
     * template.
     * 
      //    public function fulltextQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
      //        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
      //        $qb->orderBy('score', 'desc');
      //        $qb->setParameter('q', $q);
      //        return $qb->getQuery();
      //    }
     * 
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * fulltext indexes on your Channel entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"fulltext"})
     *
     *
     * @Route("/search", name="channel_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Channel');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $channels = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $channels = array();
        }

        return array(
            'channels' => $channels,
            'q' => $q,
        );
    }

    /**
     * Finds and displays a Channel entity.
     *
     * @Route("/{id}", name="channel_show")
     * @Method("GET")
     * @Template()
     * @param Channel $channel
     */
    public function showAction(Channel $channel) {

        return array(
            'channel' => $channel,
        );
    }
    
}
