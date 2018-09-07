<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Playlist;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Services\YoutubeClient;

/**
 * Playlist controller.
 *
 * @Route("/playlist")
 * @Security("has_role('ROLE_USER')")
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
     * Creates a new Playlist entity.
     *
     * @Route("/new", name="playlist_new")
     * @Method({"GET", "POST"})
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

    /**
     * Finds and displays a Playlist entity.
     *
     * @Route("/{id}/refresh", name="playlist_refresh")
     * @Method("GET")
     * @Security("has_role('ROLE_CONTENT_ADMIN')")
     * 
     * @param Playlist $playlist
     */
    public function refreshAction(Playlist $playlist, YoutubeClient $client) {
        $em = $this->getDoctrine()->getManager();
        $client->updatePlaylists(array($playlist));
        $client->playlistVideos($playlist);
        $em->flush();
        $this->addFlash('success', 'The playlist metadata and list of videos has been updated.');
        return $this->redirectToRoute('playlist_show', array('id' => $playlist->getId()));
    }

}
