<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Playlist
 * eg:
 * 
 * https://www.youtube.com/playlist?list=PLtlXC1Zi-YBslruO6ezl7lwcW9SITcoGe
 * 
 * The playlist id is the value of the list query parameter.
 * 
 * Use the google client and get a playlist by ID with these parts:
 * contentDetails, id, snippet, status
 *
 * @ORM\Table(name="playlist")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaylistRepository")
 */
class Playlist extends AbstractEntity
{
    const PLAYLIST_BASE = "https://www.youtube.com/playlist?list=";
    
    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    private $youtubeId;
        
    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $publishedAt;
           
    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="playlists")
     */
    private $channel;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=24)
     */
    private $status;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $title;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;
    
    /**
     * @var Collection|Video[]
     * @ORM\ManyToMany(targetEntity="Video", inversedBy="playlists")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos= new ArrayCollection();
    }

    public function __toString() {
        return $this->title;
    }

    /**
     * Add video
     *
     * @param Video $video
     *
     * @return Playlist
     */
    public function addVideo(Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }
    
    public function hasVideo(Video $video) {
        return $this->videos->contains($video);
    }

    /**
     * Remove video
     *
     * @param Video $video
     */
    public function removeVideo(Video $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return self::PLAYLIST_BASE . $this->youtubeId;
    }

    /**
     * Set etag
     *
     * @param string $etag
     *
     * @return Playlist
     */
    public function setEtag($etag)
    {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get etag
     *
     * @return string
     */
    public function getEtag()
    {
        return $this->etag;
    }

    /**
     * Set publishedAt
     *
     * @param \DateTime $publishedAt
     *
     * @return Playlist
     */
    public function setPublished($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->publishedAt;
    }

    /**
     * Set channelTitle
     *
     * @param string $channelTitle
     *
     * @return Playlist
     */
    public function setChannelTitle($channelTitle)
    {
        $this->channelTitle = $channelTitle;

        return $this;
    }

    /**
     * Get channelTitle
     *
     * @return string
     */
    public function getChannelTitle()
    {
        return $this->channelTitle;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Playlist
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Playlist
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Playlist
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set channel
     *
     * @param Channel $channel
     *
     * @return Playlist
     */
    public function setChannel(Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set youtubeId
     *
     * @param string $youtubeId
     *
     * @return Playlist
     */
    public function setYoutubeId($youtubeId)
    {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    /**
     * Get youtubeId
     *
     * @return string
     */
    public function getYoutubeId()
    {
        return $this->youtubeId;
    }

}
