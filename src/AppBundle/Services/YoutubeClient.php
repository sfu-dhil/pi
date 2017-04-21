<?php

namespace AppBundle\Services;

use AppBundle\AppBundle;
use AppBundle\Entity\Caption;
use AppBundle\Entity\Channel;
use AppBundle\Entity\Comment;
use AppBundle\Entity\Keyword;
use AppBundle\Entity\Playlist;
use AppBundle\Entity\Thread;
use AppBundle\Entity\Video;
use AppBundle\Entity\YoutubeEntity;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use finfo;
use Google_Client;
use Google_Service_Exception;
use Google_Service_YouTube;
use Google_Service_YouTube_Playlist as YoutubePlaylist;
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

    public function findChannel($youtubeId) {
        $channel = $this->em->getRepository(Channel::class)->findOneBy(array(
            'youtubeId' => $youtubeId,
        ));
        if (!$channel) {
            $channel = new Channel();
            $channel->setYoutubeId($youtubeId);
            $this->em->persist($channel);
            $this->em->flush($channel);
        }
        return $channel;
    }

    private function updatePlaylistMetadata(Playlist $playlist, YoutubePlaylist $item) {
        $playlist->setEtag($item->getEtag());
        $playlist->setStatus($item->getStatus()->getPrivacyStatus());
        $snippet = $item->getSnippet();
        $channel = $this->findChannel($snippet->getChannelId());
        $playlist->setChannel($channel);
        $channel->addPlaylist($playlist);

        $playlist->setTitle($snippet->getTitle());
        $playlist->setDescription($snippet->getDescription());
        $playlist->setPublishedAt(new DateTime($snippet->getPublishedAt()));
        $playlist->setRefreshed();
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
            $this->updatePlaylistMetadata($playlist, $item);
        }
    }

    /**
     * @param string $youtubeId
     * @return Video
     */
    public function findVideo($youtubeId) {
        $video = $this->em->getRepository(Video::class)->findOneBy(array(
            'youtubeId' => $youtubeId
        ));
        if (!$video) {
            $video = new Video();
            $video->setYoutubeId($youtubeId);
            $this->em->persist($video);
            $this->em->flush($video);
        }
        return $video;
    }

    private function paginateList($client, $method, $parts, $params, $callable) {
        $pageToken = null;
        $youtubeIds = array();
        $params['maxResults'] = 50;
        $params['pageToken'] = $pageToken;

        do {
            $response = $client->$method($parts, $params);
            $pageToken = $response->getNextPageToken();
            $items = $response->getItems();
            $params['pageToken'] = $pageToken;
            $youtubeIds = array_merge($youtubeIds, array_map($callable, $items));
        } while ($pageToken);
        return array_unique($youtubeIds);
    }

    /**
     * @param Collection|YoutubeEntity[] $items
     */
    public function collectionIds($items) {
        return array_map(function(YoutubeEntity $item) {
            return $item->getYoutubeId();
        }, $items->toArray());
    }

    public function playlistVideos(Playlist $playlist) {
        $oldIds = $this->collectionIds($playlist->getVideos());
        $videoIds = $this->paginateList(
                $this->getYoutubeClient()->playlistItems, 
                'listPlaylistItems', 
                'snippet', 
                array(
                    'playlistId' => $playlist->getYoutubeId(),
                    'fields' => 'items/snippet/resourceId,nextPageToken,tokenPagination',
                ),
                function($item) {
                    return $item->getSnippet()->getResourceId()->getVideoId();
                }
        );

        foreach (array_diff($oldIds, $videoIds) as $removed) {
            $video = $this->findVideo($removed);
            $playlist->removeVideo($video);
            $video->removePlaylist($playlist);
        }

        foreach (array_diff($videoIds, $oldIds) as $added) {
            $video = $this->findVideo($added);
            $playlist->addVideo($video);
            $video->addPlaylist($playlist);
        }
    }

    private function updateChannelMetadata($channel, $item) {
        $channel->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $channel->setDescription($snippet->getDescription());
        $channel->setPublishedAt(new DateTime($snippet->getPublishedAt()));
        $channel->setTitle($snippet->getTitle());
        $channel->setThumbnailUrl($snippet->getThumbnails()->getDefault()->getUrl());
        $channel->setRefreshed();
    }

    /**
     * @param Collection|Channel[] $channels
     */
    public function updateChannels($channels) {
        $map = array();
        foreach ($channels as $channel) {
            $map[$channel->getYoutubeId()] = $channel;
        }

        for ($n = 0; $n < count($map); $n += 50) {
            $ids = implode(',', array_slice(array_keys($map), $n, 50));
            $response = $this->getYoutubeClient()->channels->listChannels('id, contentDetails, snippet, statistics, status', array(
                'id' => $ids,
            ));
            foreach ($response->getItems() as $item) {
                $this->updateChannelMetadata($channel, $item);
            }
        }
    }

    private function findKeyword($tag) {
        $keyword = $this->em->getRepository(Keyword::class)->findOneBy(array('name' => $tag));
        if (!$keyword) {
            $keyword = new Keyword();
            $keyword->setName($tag);
            $this->em->persist($keyword);
            $this->em->flush($keyword);
        }
        return $keyword;
    }

    private function updateVideoMetadata($video, $item) {
        $video->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $channel = $this->findChannel($snippet->getChannelId());
        $video->setChannel($channel);
        $channel->addVideo($video);
        $video->setPublishedAt(new \DateTime($snippet->getPublishedAt()));
        $video->setTitle($snippet->getTitle());
        $video->setDescription($snippet->getDescription());
        $video->setThumbnail($snippet->getThumbnails()->getDefault()->getUrl());
        if ($snippet->getTags()) {
            foreach ($snippet->getTags() as $tag) {
                $keyword = $this->findKeyword($tag);
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

    /**
     * @param Collection|Video[] $videos
     */
    public function updateVideos($videos) {
        $map = array();
        foreach ($videos as $video) {
            $map[$video->getYoutubeId()] = $video;
        }
        for ($n = 0; $n < count($map); $n += 50) {
            $ids = implode(',', array_slice(array_keys($map), $n, 50));
            $response = $this->getYoutubeClient()->videos->listVideos('id,snippet,contentDetails,status,statistics,player', array(
                'id' => $ids
            ));
            foreach ($response->getItems() as $item) {
                $video = $map[$item->getId()];
                $this->updateVideoMetadata($video, $item);
                // @todo move this out of the inner loop for batching.
                $this->em->flush($video);
            }
        }
    }

    public function captionIds(Video $video) {
        $response = $this->getYoutubeClient()->captions->listCaptions('id', $video->getYoutubeId());
        $items = $response->getItems();
        return array_map(function($item) {
            return $item->getId();
        }, $items);
    }

    private function downloadCaption($client, $youtubeId) {
        $finfo = new finfo(FILEINFO_MIME);
        $downloadResponse = $client->download($youtubeId);
        $body = $downloadResponse->getBody();
        if (substr($finfo->buffer($body, FILEINFO_MIME_TYPE), 0, 5) !== 'text/') {
            return "";
        }
        $encoding = $finfo->buffer($body, FILEINFO_MIME_ENCODING);
        if ($encoding !== 'utf-8') {
            $body = mb_convert_encoding((string) $body, 'utf-8');
        }
        return (string) $body;
    }

    private function updateCaptionMetadata($caption, $item) {
        $caption->getVideo()->setCaptionsDownloadable(true);
        $caption->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $caption->setLastUpdated(new \DateTime($snippet->getLastUpdated()));
        $caption->setTrackKind($snippet->getTrackKind());
        $caption->setLanguage($snippet->getLanguage());
        $caption->setname($snippet->getName());
        $caption->setAudioTrackType($snippet->getAudioTrackType());
        $caption->setIsCC($snippet->getIsCC());
        $caption->setIsDraft($snippet->getIsDraft());
        $caption->setIsAutoSynced($snippet->getIsAutoSynced());
        $caption->setRefreshed();
    }

    public function updateCaption(Caption $caption) {
        $client = $this->getYoutubeClient()->captions;

        try {
            $listResponse = $client->listCaptions('id,snippet', $caption->getVideo()->getYoutubeId(), array(
                'id' => $caption->getYoutubeId()
            ));
        } catch (Google_Service_Exception $e) {
            $caption->getVideo()->setCaptionsDownloadable(false);
            return;
        }
        $listItems = $listResponse->getItems();
        if (count($listItems) !== 1) {
            throw new Exception("Expected one caption snippet in search. Found " . count($listItems));
        }
        $this->updateCaptionMetadata($caption, $listItems[0]);
        try{
            $this->downloadCaption($client, $caption->getYoutubeId());
        } catch (Google_Service_Exception $e) {
            $caption->getVideo()->setCaptionsDownloadable(false);
        }
    }

    public function updateThreadIds(Video $video) {
        $oldIds = $this->collectionIds($video->getThreads()); 
        $threadIds = $this->paginateList(
                $this->getYoutubeClient()->commentThreads, 
                'listCommentThreads', 
                'id', 
                array(
                    'videoId' => $video->getYoutubeId(), 
                ),
                function($item) {
                    return $item->getId();
                }                
        );

        $threadRepo = $this->em->getRepository(Thread::class);
        foreach (array_diff($oldIds, $threadIds) as $removed) {
            $thread = $threadRepo->findOneBy(array('youtubeId' => $removed));
            if ($thread) {
                $video->removeThread($thread);
            }
            $this->em->remove($thread);
        }

        foreach (array_diff($threadIds, $oldIds) as $added) {
            $thread = new Thread();
            $thread->setYoutubeId($added);
            $thread->setVideo($video);
            $video->addThread($thread);
            $this->em->persist($thread);
        }
        $this->em->flush();
    }
    
    private function findComment($youtubeId) {
        $comment = $this->em->getRepository(Comment::class)->findOneBy(array(
            'youtubeId' => $youtubeId,
        ));
        if (!$comment) {
            $comment = new Comment();
            $comment->setYoutubeId($youtubeId);
            $this->em->persist($comment);
        }
        return $comment;
    }
    
    private function commentMetadata($comment, $item) {
        $comment->setEtag($item->getEtag());
        $snippet = $item->getSnippet();
        $channelId = $snippet->getAuthorChannelId()['value'];
        $channel = $this->findChannel($channelId);
        $channel->addComment($comment);
        $comment->setChannel($channel);
        $comment->setAuthorName($snippet->getAuthorDisplayName());
        $comment->setPublishedAt(new DateTime($snippet->getPublishedAt()));
        $comment->setLikes($snippet->getLikeCount());
        $comment->setUpdatedAt(new DateTime($snippet->getUpdatedAt()));
        $comment->setContent($snippet->getTextDisplay());
        $comment->setRefreshed();
    }

    /**
     * @param Collection|Thread[] $thread
     */
    public function updateThreads($threads) {
        $client = $this->getYoutubeClient()->commentThreads;
        $map = array();
        foreach ($threads as $thread) {
            $map[$thread->getYoutubeId()] = $thread;
        }
        for ($n = 0; $n < count($threads); $n += 50) {
            $ids = implode(',', array_slice(array_keys($map), $n, 50));
            $response = $client->listCommentThreads('id,snippet', array(
                'id' => $ids
            ));
            foreach ($response->getItems() as $item) {
                $thread = $map[$item->getId()];
                $thread->setEtag($item->getEtag());
                $snippet = $item->getSnippet();
                $thread->setReplyCount($snippet->getTotalReplyCount());
                $top = $snippet->getTopLevelComment();
                $topComment = $this->findComment($top->getId());
                $thread->setRoot($topComment);
                $topComment->setThread($thread);
                $thread->setRefreshed();
            }
            $this->updateComments(array($thread->getRoot()));
        }
        $this->em->flush();
    }
    
    public function updateCommentIds(Thread $thread) {
        $oldIds = $this->collectionIds($thread->getReplies());
        $commentIds = $this->paginateList(
                $this->getYoutubeClient()->comments,
                'listComments',
                'id,snippet',
                array('parentId' => $thread->getYoutubeId()),
                function($item) { return $item->getId();}
        );
        
        foreach(array_diff($oldIds, $commentIds) as $removed) {
            $comment = $this->findComment($removed);
            $thread->removeReply($comment);
            $comment->setThread(null);
        }
        
        foreach(array_diff($commentIds, $oldIds) as $added) {
            $comment = $this->findComment($added);
            $comment->setThread($thread);
            $thread->addReply($comment);
        }
    }

    /**
     * 
     * @param Collection|Comment[] $comments
     */
    public function updateComments($comments) {
        $client = $this->getYoutubeClient()->comments;
        $map = array();
        foreach($comments as $comment) {
            $map[$comment->getYoutubeId()] = $comment;
        }
        for($n = 0; $n < count($comments); $n+= 50) {
            $ids = implode(',', array_slice(array_keys($map), $n, 50));
            $response = $client->listComments('id,snippet', array(
                'id' => $ids
            ));
            foreach($response->getItems() as $item) {
                $comment = $map[$item->getId()];
                $this->commentMetadata($comment, $item);
            }
            $this->em->flush();
        }        
    }

}
