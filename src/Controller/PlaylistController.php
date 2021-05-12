<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\Entity\Playlist;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Bundle\PaginatorBundle\Definition\PaginatorAwareInterface;
use Nines\UtilBundle\Controller\PaginatorTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Playlist controller.
 *
 * @Route("/playlist")
 */
class PlaylistController extends AbstractController implements PaginatorAwareInterface {
    use PaginatorTrait;

    /**
     * Lists all Playlist entities.
     *
     * @Route("/", name="playlist_index", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function indexAction(Request $request, EntityManagerInterface $em) {
        $dql = 'SELECT e FROM App:Playlist e ORDER BY e.id';
        $query = $em->createQuery($dql);

        $playlists = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'playlists' => $playlists,
            'repo' => $em->getRepository(Video::class),
        ];
    }

    /**
     * Finds and displays a Playlist entity.
     *
     * @Route("/{id}", name="playlist_show", methods={"GET"})
     *
     * @Template
     *
     * @return array
     */
    public function showAction(Request $request, Playlist $playlist) {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository(Video::class);
        $query = $repo->findVideosQuery($this->getUser(), [
            'type' => Playlist::class,
            'id' => $playlist->getId(),
        ]);

        $videos = $this->paginator->paginate($query, $request->query->getint('page', 1), 20);

        return [
            'playlist' => $playlist,
            'videos' => $videos,
        ];
    }
}
