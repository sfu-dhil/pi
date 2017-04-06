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
    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     */
    private $url;
        
    /**
     * @var string
     * @ORM\Column(type="string", length=64)
     */
    private $etag;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $published;
       
    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="playlists")
     */
    private $channel;
    
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */    
    private $channelTitle;
        
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
     * Set url
     *
     * @param string $url
     *
     * @return Playlist
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * Set published
     *
     * @param \DateTime $published
     *
     * @return Playlist
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return \DateTime
     */
    public function getPublished()
    {
        return $this->published;
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
     * @param \AppBundle\Entity\Channel $channel
     *
     * @return Playlist
     */
    public function setChannel(\AppBundle\Entity\Channel $channel = null)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return \AppBundle\Entity\Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }
}
