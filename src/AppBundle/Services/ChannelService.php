<?php

namespace AppBundle\Services;

use AppBundle\Entity\Channel;
use DateTime;
use Exception;

/**
 * Description of Channel
 *
 * @author michael
 */
class ChannelService extends AbstractYoutubeClient {

    const PART='id, contentDetails, snippet, statistics, status';
    
    public function __construct($youtubeKey) {
        parent::__construct($youtubeKey);
    }
    
    protected function fromItem($item, Channel $channel) {
        $channel->setYoutubeId($item->getId());
        $channel->setDescription($item->getSnippet()->getDescription());
        $channel->setPublished(new DateTime($item->getSnippet()->getPublishedAt()));
        $channel->setTitle($item->getSnippet()->getTitle());
        $channel->setThumbnailUrl($item->getSnippet()->getThumbnails()->getMedium()->getUrl());
    }
    
    public function update(Channel $channel) {
        $youtube = $this->getClient();
        $query = array();
        if($channel->getYoutubeId()) {
            $query['id'] = $channel->getYoutubeId();
        }
        if($channel->getTitle()) {
            $query['forUsername'] = $channel->getTitle();
        }
        $response = $youtube->channels->listChannels(self::PART, $query);
        $items = $response->getItems();
        if( count($items) !== 1) {
            throw new Exception("Expected one channel in search. Found " . count($items));
        }
        $this->fromItem($items[0], $channel);
    }
    
}
