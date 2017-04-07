<?php

namespace AppBundle\Services;

use AppBundle\Entity\Video;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;

/**
 * Description of Channel
 *
 * @author michael
 */
class VideoService extends AbstractYoutubeClient {

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

    protected function fromItem($item, Video $video) {
        $snippet = $item->getSnippet;
        
        $video->setPublishedAt(new DateTime($snipet->getPublishedAt()));
        
    }

    public function update(Video $video) {
        $youtube = $this->getClient();

        $response = $youtube->videos->listVideos(self::PART, array(
            'id' => $video->getYoutubeId(),
        ));
        $items = $response->getItems();
        if (count($items) !== 1) {
            throw new Exception("Expected one playlist in search. Found " . count($items) . ".");
        }
        dump($items[0]);
        $this->fromItem($items[0], $video);
    }

}
