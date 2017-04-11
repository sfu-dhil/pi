<?php

namespace AppBundle\Services;

use AppBundle\AppBundle;
use AppBundle\Entity\Caption;
use AppBundle\Entity\Channel;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\Playlist;
use AppBundle\Entity\Video;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Google_Client;
use Google_Service_YouTube;
use Monolog\Logger;
use Nines\UserBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Validator\Constraints\Collection;

/**
 * Description of AbstractYoutubeClient
 *
 * @author michael
 */
class YoutubeClient {

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Google_Service_YouTube
     */
    private $oauthClient;

    /**
     * Path to the API secrets file.
     * 
     * @var string
     */
    private $oauthFile;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var AuthorizationChecker
     */
    private $authChecker;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @var Router
     */
    private $router;
    
    /**
     * @var User
     */
    private $user;

    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }

    public function setDoctrine(Registry $registry) {
        $this->em = $registry->getManager();
    }

    public function setAuthChecker(AuthorizationChecker $authChecker) {
        $this->authChecker = $authChecker;
    }

    public function setTokenStorage(TokenStorage $tokenStorage) {
        $this->tokenStorage = $tokenStorage;
    }

    public function setRouter(Router $router) {
        $this->router = $router;
    }

    public function setUser(User $user) {
        $this->user = $user;
    }

    protected function getUser() {
        if (!$this->user) {
            if (!$this->authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
                return null;
            }
            $this->user = $this->tokenStorage->getToken()->getUser();
        }
        return $this->user;
    }

    public function getClient() {
        if (!$this->oauthClient) {
            $user = $this->getUser();
            $client = new Google_Client();
            $client->setAuthConfig($this->oauthFile);
            $client->setAccessType("offline");
            $client->setIncludeGrantedScopes(true);   // incremental auth
            $client->addScope(Google_Service_YouTube::YOUTUBE);
            $client->addScope(Google_Service_YouTube::YOUTUBE_FORCE_SSL);
            $client->addScope(Google_Service_YouTube::YOUTUBE_READONLY);
            $client->setRedirectUri($this->router->generate('oauth2callback', array(), UrlGeneratorInterface::ABSOLUTE_URL));
            if ($user->hasData(AppBundle::AUTH_USER_KEY)) {
                $client->setAccessToken($user->getData(AppBundle::AUTH_USER_KEY));
            }
            $this->oauthClient = $client;
        }
        return $this->oauthClient;
    }

    public function getYoutubeClient() {
        $client = $this->getClient();
        return new Google_Service_YouTube($client);
    }

    public function __construct($oauthFile) {
        $this->oauthFile = $oauthFile;
    }

    /**
     * @param Collection|Playlist[] $playlists
     */
    public function updatePlaylists($playlists) {
        $map = array();
        foreach ($playlists as $playlist) {
            $map[$playlist->getYoutubeId()] = $playlist;
        }
        $ids = implode(',', array_keys($map));
        $response = $this->getYoutubeClient()->playlists->listPlaylists('id, snippet, status, contentDetails', array(
            'id' => $ids,
        ));
        foreach ($response->getItems() as $item) {
            $playlist = $map[$item->getId()];
            $playlist->setEtag($item->getEtag());
            $playlist->setStatus($item->getStatus()->getPrivacyStatus());
            $snippet = $item->getSnippet();
            $channelId = $snippet->getChannelId();
            $channel = $this->em->getRepository(Channel::class)->findOneBy(array(
                'youtubeId' => $snippet->getChannelId()
            ));
            if (!$channel) {
                $channel = new Channel();
                $channel->setYoutubeId($channelId);
                $this->em->persist($channel);
            }
            $playlist->setChannel($channel);
            $channel->addPlaylist($playlist);

            $playlist->setTitle($snippet->getTitle());
            $playlist->setDescription($snippet->getDescription());
            $playlist->setPublishedAt(new DateTime($snippet->getPublishedAt()));
            $playlist->setRefreshed();
        }
    }

    public function playlistVideos(Playlist $playlist) {
        
        $videoRepo = $this->em->getRepository(Video::class);
        
        $oldIds = array_map(function(Video $video){
            return $video->getYoutubeId();
            
        }, $playlist->getVideos()->toArray());
        
        $token = null;
        $videoIds = array();
        do {
            $response = $this->getYoutubeClient()->playlistItems->listPlaylistItems('snippet', array(
                'playlistId' => $playlist->getYoutubeId(),
                'maxResults' => 50,
                'pageToken' => $token,
                'fields' => 'items/snippet/resourceId,nextPageToken,pageInfo,prevPageToken,tokenPagination',
            ));
            $token = $response->getNextPageToken();
            $items = $response->getItems();
            $videoIds = array_merge($videoIds, array_map(function($item) {
                        return $item->getSnippet()->getResourceId()->getVideoId();
                    }, $items));
        } while ($token);
        
        foreach($videoIds as $id) {
            $video = $videoRepo->findOneBy(array('youtubeId' => $id));
            if( ! $video) {
                $video = new Video();
                $video->setYoutubeId($id);
                $this->em->persist($video);
            }
            if( ! in_array($id, $oldIds)) {
                $playlist->addVideo($video);
                $video->addPlaylist($playlist);
            }
        }
        
        foreach($oldIds as $id) {
            if( ! in_array($id, $videoIds)) {
                $oldVideo = $videoRepo->findOneBy(array('youtubeId' => $id));
                $playlist->removeVideo($oldVideo);
                $oldVideo->removePlaylist($playlist);
            }
        }
    }

    public function updateChannel(Channel $channel) {
        $response = $this->getYoutubeClient()->channels->listChannels('id, contentDetails, snippet, statistics, status', array(
            'id' => $channel->getYoutubeId(),
        ));
        $items = $response->getItems();
        if (count($items) !== 1) {
            throw new Exception("Expected one channel in search. Found " . count($items));
        }
        $item = $items[0];
        $channel->setYoutubeId($item->getId());
        $channel->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $channel->setDescription($snippet->getDescription());
        $channel->setPublishedAt(new DateTime($snippet->getPublishedAt()));
        $channel->setTitle($snippet->getTitle());
        $channel->setThumbnailUrl($snippet->getThumbnails()->getDefault()->getUrl());
        $channel->setRefreshed();
    }

    public function updateVideo(Video $video) {
        $response = $this->getYoutubeClient()->videos->listVideos('id,snippet,contentDetails,status,statistics,player', array(
            'id' => $video->getYoutubeId(),
        ));

        $items = $response->getItems();
        if (count($items) !== 1) {
            throw new Exception("Expected one video in search. Found " . count($items) . ".");
        }
        $item = $items[0];
        $video->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $channelId = $snippet->getChannelId();
        $channelRepo = $this->em->getRepository(Channel::class);
        $channel = $channelRepo->findOneBy(array('youtubeId' => $channelId));
        if (!$channel) {
            $channel = new Channel();
            $channel->setYoutubeId($channelId);
            $this->em->persist($channel);
        }
        $video->setChannel($channel);
        $channel->addVideo($video);
        $video->setPublishedAt(new \DateTime($snippet->getPublishedAt()));
        $video->setTitle($snippet->getTitle());
        $video->setDescription($snippet->getDescription());
        $video->setThumbnail($snippet->getThumbnails()->getDefault()->getUrl());
        $keywordRepo = $this->em->getRepository(Keyword::class);
        if ($snippet->getTags()) {
            foreach ($snippet->getTags() as $tag) {
                $keyword = $keywordRepo->findOneBy(array('name' => $tag));
                if (!$keyword) {
                    $keyword = new Keyword();
                    $keyword->setName($tag);
                    $this->em->persist($keyword);
                }
                $video->addKeyword($keyword);
                $keyword->addVideo($video);
            }
        }
        $detail = $item->getContentDetails();
        $video->setDuration($detail->getDuration());
        $video->setDefinition($detail->getDefinition());
        $video->setCaptionsAvailable($detail->getCaption() == "true");
        $status = $item->getStatus();
        $video->setLicense($status->getLicense());
        $video->setEmbeddable($status->getEmbeddable());
        $stats = $item->getStatistics();
        if ($stats) {
            $video->setCommentCount($stats->getCommentCount());
            $video->setDislikeCount($stats->getDislikeCount());
            $video->setFavouriteCount($stats->getFavoriteCount());
            $video->setLikeCount($stats->getLikeCount());
            $video->setViewCount($stats->getViewCount());
        }
        $video->setPlayer($item->getPlayer()->getEmbedHtml());
        $video->setRefreshed();
    }

    public function captionIds(Video $video) {
        $response = $this->getYoutubeClient()->captions->listCaptions('id', $video->getYoutubeId(), array(
        ));
        $items = $response->getItems();
        return array_map(function($item) {
            return $item->getId();
        }, $items);
    }

    public function updateCaption(Caption $caption) {
        $client = $this->getYoutubeClient()->captions;

        $listResponse = $client->listCaptions('id,snippet', $caption->getVideo()->getYoutubeId(), array(
            'id' => $caption->getYoutubeId()
        ));
        $listItems = $listResponse->getItems();
        if (count($listItems) !== 1) {
            throw new Exception("Expected one caption snippet in search. Found " . count($listItems));
        }
        $listItem = $listItems[0];
        $caption->setEtag($listItem->getEtag());
        $snippet = $listItem->getSnippet();
        $caption->setLastUpdated(new \DateTime($snippet->getLastUpdated()));
        $caption->setTrackKind($snippet->getTrackKind());
        $caption->setLanguage($snippet->getLanguage());
        $caption->setname($snippet->getName());
        $caption->setAudioTrackType($snippet->getAudioTrackType());
        $caption->setIsCC($snippet->getIsCC());
        $caption->setIsDraft($snippet->getIsDraft());
        $caption->setIsAutoSynced($snippet->getIsAutoSynced());

        $downloadResponse = $client->download($caption->getYoutubeId());
        $caption->setContent((string) ($downloadResponse->getBody()));
        $caption->setRefreshed();
    }

}
