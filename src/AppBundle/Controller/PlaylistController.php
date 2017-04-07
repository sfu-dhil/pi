<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use AppBundle\Entity\Video;
use AppBundle\Form\PlaylistType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Playlist controller.
 *
 * @Route("/playlist")
 */
class PlaylistController extends Controller {

    /**
     * Lists all Playlist entities.
     *
     * @Route("/", name="playlist_index")
     * @Method("GET")
     * @Template()
     * @param Request $request
     */
    public function indexAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $dql = 'SELECT e FROM AppBundle:Playlist e ORDER BY e.id';
        $query = $em->createQuery($dql);
        $paginator = $this->get('knp_paginator');
        $playlists = $paginator->paginate($query, $request->query->getint('page', 1), 25);

        return array(
            'playlists' => $playlists,
        );
    }

    /**
     * Full text search for Playlist entities.
     *
     * To make this work, add a method like this one to the 
     * AppBundle:Playlist repository. Replace the fieldName with
     * something appropriate, and adjust the generated search.html.twig
     * template.
     * 
      //    public function searchQuery($q) {
      //        $qb = $this->createQueryBuilder('e');
      //        $qb->addSelect("MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') as score");
      //        $qb->add('where', "MATCH_AGAINST (e.name, :q 'IN BOOLEAN MODE') > 0.5");
      //        $qb->orderBy('score', 'desc');
      //        $qb->setParameter('q', $q);
      //        return $qb->getQuery();
      //    }
     * 
     * Requires a MatchAgainst function be added to doctrine, and appropriate
     * search indexes on your Playlist entity.
     *     ORM\Index(name="alias_name_idx",columns="name", flags={"search"})
     *
     *
     * @Route("/search", name="playlist_search")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle:Playlist');
        $q = $request->query->get('q');
        if ($q) {
            $query = $repo->fulltextQuery($q);
            $paginator = $this->get('knp_paginator');
            $playlists = $paginator->paginate($query, $request->query->getInt('page', 1), 25);
        } else {
            $playlists = array();
        }

        return array(
            'playlists' => $playlists,
            'q' => $q,
        );
    }

    /**
     * Creates a new Playlist entity.
     *
     * @Route("/new", name="playlist_new")
     * @Method({"GET", "POST"})
     * @Template()
     * @param Request $request
     */
    public function newAction(Request $request) {
        $playlist = new Playlist();
        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service = $this->get('yt.playlist');
            $service->update($playlist);
            $em = $this->getDoctrine()->getManager();
            $em->persist($playlist);
            $em->flush();

            $this->addFlash('success', 'The new playlist was created.');
            return $this->redirectToRoute('playlist_show', array('id' => $playlist->getId()));
        }

        return array(
            'playlist' => $playlist,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/refresh", name="playlist_refresh")
     * @Method("GET")
     * @Template()
     * @param Request $request
     * @param Playlist $playlist
     */
    public function refreshAction(Request $request, Playlist $playlist) {
        $service = $this->get('yt.playlist');
        $videoIds = $service->getVideoIds($playlist);
        dump($videoIds);
        $em = $this->getDoctrine();
        $repo = $em->getRepository(Video::class);
        foreach($videoIds as $videoId) {
            $video = $repo->findOneBy(array('youtubeId' => $videoId));
            if( ! $video) {
                
            }
        }
        return $this->redirectToRoute('playlist_show', array('id' => $playlist->getId()));
    }

    /**
     * Finds and displays a Playlist entity.
     *
     * @Route("/{id}", name="playlist_show")
     * @Method("GET")
     * @Template()
     * @param Playlist $playlist
     */
    public function showAction(Playlist $playlist) {

        return array(
            'playlist' => $playlist,
        );
    }

}
