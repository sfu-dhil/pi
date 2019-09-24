<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use AppBundle\Entity\Video;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\YoutubeClient;

/**
 * Playlist controller.
 *
 * @Route("/playlist")
 */
class PlaylistController extends Controller {

    /**
     * Lists all Playlist entities.
     *
     * @Route("/", name="playlist_index", methods={"GET"})
     *
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
            'repo' => $em->getRepository(Video::class),
        );
    }

    /**
     * Creates a new Playlist entity.
     *
     * @Route("/new", name="playlist_new", methods={"GET","POST"})
     *
     * @Template()
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Request $request
     */
    public function newAction(Request $request) {
        $playlist = new Playlist();
        $form = $this->createForm('AppBundle\Form\PlaylistType', $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
     * Finds and displays a Playlist entity.
     *
     * @Route("/{id}", name="playlist_show", methods={"GET"})
     *
     * @Template()
     * @param Playlist $playlist
     */
    public function showAction(Playlist $playlist) {
        $repo = $this->getDoctrine()->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), array(
            'type' => Playlist::class,
            'id' => $playlist->getId(),
        ));
        return array(
            'playlist' => $playlist,
            'videos' => $query->execute(),
        );
    }

}
