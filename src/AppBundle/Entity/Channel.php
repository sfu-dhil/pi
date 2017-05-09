<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Channel
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChannelRepository")
 */
class Channel extends YoutubeEntity {

    const CHANNEL_BASE = "https://www.youtube.com/channel/";
    
    /**
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $thumbnailUrl;
    
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
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Collection|Video[]
     * @ORM\OneToMany(targetEntity="Video", mappedBy="channel")
     */
    private $videos;

    /**
     * @var Collection|Playlist[]
     * @ORM\OneToMany(targetEntity="Playlist", mappedBy="channel")
     */
    private $playlists;

    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
        $this->playlists = new ArrayCollection();
    }

    public function __toString() {
        if( $this->title ) {
            return $this->title;
        } 
        if( $this->youtubeId) {
            return $this->youtubeId;
        }
        if( $this->id) {
            return $this->id;
        }
        return "";
    }

    /**
     * Add video
     *
     * @param Video $video
     *
     * @return Channel
     */
    public function addVideo(Video $video) {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param Video $video
     */
    public function removeVideo(Video $video) {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return Collection
     */
    public function getVideos() {
        return $this->videos;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return self::CHANNEL_BASE . $this->youtubeId;
    }

    /**
     * Set thumbnailUrl
     *
     * @param string $thumbnailUrl
     *
     * @return Channel
     */
    public function setThumbnailUrl($thumbnailUrl)
    {
        $this->thumbnailUrl = $thumbnailUrl;

        return $this;
    }

    /**
     * Get thumbnailUrl
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->thumbnailUrl;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Channel
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
     * @return Channel
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
     * Set publishedAt
     *
     * @param DateTime $publishedAt
     *
     * @return Channel
     */
    public function setPublishedAt($publishedAt)
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt
     *
     * @return DateTime
     */
    public function getPublishedAt()
    {
        return $this->publishedAt;
    }

    /**
     * Add playlist
     *
     * @param Playlist $playlist
     *
     * @return Channel
     */
    public function addPlaylist(Playlist $playlist)
    {
        $this->playlists[] = $playlist;

        return $this;
    }

    /**
     * Remove playlist
     *
     * @param Playlist $playlist
     */
    public function removePlaylist(Playlist $playlist)
    {
        $this->playlists->removeElement($playlist);
    }

    /**
     * Get playlists
     *
     * @return Collection
     */
    public function getPlaylists()
    {
        return $this->playlists;
    }
}
