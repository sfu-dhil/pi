<?php

namespace AppBundle\Services;

use AppBundle\Entity\Channel;
use AppBundle\Entity\Playlist;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * Description of Channel
 *
 * @author michael
 */
class PlaylistService extends AbstractYoutubeClient {

    const PART = 'id, snippet, status, contentDetails';

    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct($youtubeKey) {
        parent::__construct($youtubeKey);
    }

    public function setDoctrine(Registry $registry) {
        $this->em = $registry->getManager();
    }

    protected function fromItem($item, Playlist $playlist) {
        $playlist->setStatus($item->getStatus()->getPrivacyStatus());
        $snippet = $item->getSnippet();
        $playlist->setTitle($snippet->getTitle());
        $playlist->setDescription($snippet->getDescription());
        $playlist->setPublished(new DateTime($snippet->getPublishedAt()));
        $channel = $this->em->getRepository(Channel::class)->findOneBy(array(
            'youtubeId' => $snippet->getChannelId()
        ));
        $playlist->setChannel($channel);
    }

    public function update(Playlist $playlist) {
        $youtube = $this->getClient();

        $response = $youtube->playlists->listPlaylists(self::PART, array(
            'id' => $playlist->getYoutubeId(),
        ));
        $items = $response->getItems();
        if (count($items) !== 1) {
            throw new Exception("Expected one playlist in search. Found " . count($items) . ".");
        }
        $this->fromItem($items[0], $playlist);
    }

    public function getVideoIds(Playlist $playlist) {
        $youtube = $this->getClient();
        $token = null;
        $result = array();
        
        do {
            $response = $youtube->playlistItems->listPlaylistItems('snippet', array(
                'playlistId' => $playlist->getYoutubeId(),
                'maxResults' => 50,
                'pageToken' => $token,
            ));
            $token = $response->getNextPageToken();
            $items = $response->getItems();
            
            $result = array_merge($result, array_map(function($item){return $item->getSnippet()->getResourceId()->getVideoId();}, $items));
        } while ($token);
        
        return $result;
    }

}
