<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Playlist
 * eg:.
 *
 * https://www.youtube.com/playlist?list=PLtlXC1Zi-YBslruO6ezl7lwcW9SITcoGe
 *
 * The playlist id is the value of the list query parameter.
 *
 * Use the google client and get a playlist by ID with these parts:
 * contentDetails, id, snippet, status
 *
 * @ORM\Table(name="playlist")
 * @ORM\Entity(repositoryClass="App\Repository\PlaylistRepository")
 */
class Playlist extends YoutubeEntity {
    public const PLAYLIST_BASE = 'https://www.youtube.com/playlist?list=';

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="playlists")
     * @ORM\JoinColumn(nullable=true)
     */
    private $channel;

    /**
     * @var string
     * @ORM\Column(type="string", length=24, nullable=true)
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true, nullable=true)
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
        $this->videos = new ArrayCollection();
    }

    public function __toString() : string {
        if ($this->title) {
            return $this->title;
        }

        return $this->youtubeId;
    }

    /**
     * Add video.
     *
     * @return Playlist
     */
    public function addVideo(Video $video) {
        if ( ! $this->videos->contains($video)) {
            $this->videos[] = $video;
        }

        return $this;
    }

    public function hasVideo(Video $video) {
        return $this->videos->contains($video);
    }

    /**
     * Remove video.
     */
    public function removeVideo(Video $video) : void {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos.
     *
     * @return Collection
     */
    public function getVideos() {
        return $this->videos;
    }

    /**
     * Get url.
     *
     * @return string
     */
    public function getUrl() {
        return self::PLAYLIST_BASE . $this->youtubeId;
    }

    /**
     * Set channelTitle.
     *
     * @param string $channelTitle
     *
     * @return Playlist
     */
    public function setChannelTitle($channelTitle) {
        $this->channelTitle = $channelTitle;

        return $this;
    }

    /**
     * Get channelTitle.
     *
     * @return string
     */
    public function getChannelTitle() {
        return $this->channelTitle;
    }

    /**
     * Set status.
     *
     * @param string $status
     *
     * @return Playlist
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Playlist
     */
    public function setTitle($title) {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Playlist
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set channel.
     *
     * @param Channel $channel
     *
     * @return Playlist
     */
    public function setChannel(?Channel $channel = null) {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel.
     *
     * @return Channel
     */
    public function getChannel() {
        return $this->channel;
    }

    /**
     * Set publishedAt.
     *
     * @param DateTime $publishedAt
     *
     * @return Playlist
     */
    public function setPublishedAt($publishedAt) {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    /**
     * Get publishedAt.
     *
     * @return DateTime
     */
    public function getPublishedAt() {
        return $this->publishedAt;
    }
}
