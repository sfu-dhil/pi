<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateInterval;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UserBundle\Entity\User;

/**
 * Video.
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="App\Repository\VideoRepository")
 */
class Video extends YoutubeEntity {
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private $hidden;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

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
     * @var string
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $thumbnail;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $duration;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $definition;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $captionsAvailable;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $captionsDownloadable;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=true)
     */
    private $license;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $embeddable;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $viewCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likeCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dislikeCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $favouriteCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $commentCount;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $player;

    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="videos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $channel;

    /**
     * @var Figuration
     * @ORM\ManyToOne(targetEntity="Figuration", inversedBy="videos")
     * @ORM\JoinColumn(nullable=true)
     */
    private $figuration;

    /**
     * @var Caption[]|Collection
     * @ORM\OneToMany(targetEntity="Caption", mappedBy="video")
     */
    private $captions;

    /**
     * @var Collection|Keyword[]
     * @ORM\ManyToMany(targetEntity="Keyword", inversedBy="videos")
     * @ORM\OrderBy({"label"="ASC"})
     */
    private $keywords;

    /**
     * @var Collection|Playlist[]
     * @ORM\ManyToMany(targetEntity="Playlist", mappedBy="videos")
     */
    private $playlists;

    /**
     * @var Collection|VideoProfile[]
     * @ORM\OneToMany(targetEntity="VideoProfile", mappedBy="video")
     */
    private $videoProfiles;

    /**
     * @var Collection|ScreenShot[]
     * @ORM\OneToMany(targetEntity="ScreenShot", mappedBy="video")
     */
    private $screenShots;

    public function __construct() {
        parent::__construct();
        $this->hidden = false;
        $this->keywords = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->captions = new ArrayCollection();
        $this->videoProfiles = new ArrayCollection();
        $this->screenShots = new ArrayCollection();
    }

    public function __toString() {
        if ($this->title) {
            return $this->title;
        }

        return $this->youtubeId;
    }

    /**
     * Set channel.
     *
     * @return Video
     */
    public function setChannel(Channel $channel) {
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
     * Add keyword.
     *
     * @return Video
     */
    public function addKeyword(Keyword $keyword) {
        if ( ! $this->hasKeyword($keyword)) {
            $this->keywords[] = $keyword;
        }

        return $this;
    }

    /**
     * Remove keyword.
     */
    public function removeKeyword(Keyword $keyword) : void {
        $this->keywords->removeElement($keyword);
    }

    /**
     * Get keywords.
     *
     * @return Collection|Keyword[]
     */
    public function getKeywords() {
        return $this->keywords;
    }

    public function hasKeyword(Keyword $keyword) {
        return $this->keywords->contains($keyword);
    }

    /**
     * Add playlist.
     *
     * @return Video
     */
    public function addPlaylist(Playlist $playlist) {
        $this->playlists[] = $playlist;

        return $this;
    }

    /**
     * Remove playlist.
     */
    public function removePlaylist(Playlist $playlist) : void {
        $this->playlists->removeElement($playlist);
    }

    /**
     * Get playlists.
     *
     * @return Collection
     */
    public function getPlaylists() {
        return $this->playlists;
    }

    /**
     * Set publishedAt.
     *
     * @param DateTime $publishedAt
     *
     * @return Video
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

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Video
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
     * @return Video
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
     * Set thumbnail.
     *
     * @param string $thumbnail
     *
     * @return Video
     */
    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail.
     *
     * @return string
     */
    public function getThumbnail() {
        return $this->thumbnail;
    }

    /**
     * Set duration.
     *
     * @param string $duration
     *
     * @return Video
     */
    public function setDuration($duration) {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration.
     *
     * @param mixed $object
     *
     * @return string
     */
    public function getDuration($object = false) {
        if ($object) {
            return new DateInterval($this->duration);
        }

        return $this->duration;
    }

    /**
     * Set definition.
     *
     * @param string $definition
     *
     * @return Video
     */
    public function setDefinition($definition) {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition.
     *
     * @return string
     */
    public function getDefinition() {
        return $this->definition;
    }

    /**
     * Set license.
     *
     * @param string $license
     *
     * @return Video
     */
    public function setLicense($license) {
        $this->license = $license;

        return $this;
    }

    /**
     * Get license.
     *
     * @return string
     */
    public function getLicense() {
        return $this->license;
    }

    /**
     * Set embeddable.
     *
     * @param bool $embeddable
     *
     * @return Video
     */
    public function setEmbeddable($embeddable) {
        $this->embeddable = $embeddable;

        return $this;
    }

    /**
     * Get embeddable.
     *
     * @return bool
     */
    public function getEmbeddable() {
        return $this->embeddable;
    }

    /**
     * Set viewCount.
     *
     * @param int $viewCount
     *
     * @return Video
     */
    public function setViewCount($viewCount) {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * Get viewCount.
     *
     * @return int
     */
    public function getViewCount() {
        return $this->viewCount;
    }

    /**
     * Set likeCount.
     *
     * @param int $likeCount
     *
     * @return Video
     */
    public function setLikeCount($likeCount) {
        $this->likeCount = $likeCount;

        return $this;
    }

    /**
     * Get likeCount.
     *
     * @return int
     */
    public function getLikeCount() {
        return $this->likeCount;
    }

    /**
     * Set dislikeCount.
     *
     * @param int $dislikeCount
     *
     * @return Video
     */
    public function setDislikeCount($dislikeCount) {
        $this->dislikeCount = $dislikeCount;

        return $this;
    }

    /**
     * Get dislikeCount.
     *
     * @return int
     */
    public function getDislikeCount() {
        return $this->dislikeCount;
    }

    /**
     * Set favouriteCount.
     *
     * @param int $favouriteCount
     *
     * @return Video
     */
    public function setFavouriteCount($favouriteCount) {
        $this->favouriteCount = $favouriteCount;

        return $this;
    }

    /**
     * Get favouriteCount.
     *
     * @return int
     */
    public function getFavouriteCount() {
        return $this->favouriteCount;
    }

    /**
     * Set commentCount.
     *
     * @param int $commentCount
     *
     * @return Video
     */
    public function setCommentCount($commentCount) {
        $this->commentCount = $commentCount;

        return $this;
    }

    /**
     * Get commentCount.
     *
     * @return int
     */
    public function getCommentCount() {
        return $this->commentCount;
    }

    /**
     * Set player.
     *
     * @param string $player
     *
     * @return Video
     */
    public function setPlayer($player) {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player.
     *
     * @return string
     */
    public function getPlayer() {
        return $this->player;
    }

    /**
     * Set captionsAvailable.
     *
     * @param bool $captionsAvailable
     *
     * @return Video
     */
    public function setCaptionsAvailable($captionsAvailable) {
        $this->captionsAvailable = $captionsAvailable;

        return $this;
    }

    /**
     * Get captionsAvailable.
     *
     * @return bool
     */
    public function getCaptionsAvailable() {
        return $this->captionsAvailable;
    }

    /**
     * Add caption.
     *
     * @return Video
     */
    public function addCaption(Caption $caption) {
        $this->captions[] = $caption;

        return $this;
    }

    /**
     * Remove caption.
     */
    public function removeCaption(Caption $caption) : void {
        $this->captions->removeElement($caption);
    }

    /**
     * Get captions.
     *
     * @param null|mixed $kind
     *
     * @return Collection
     */
    public function getCaptions($kind = null) {
        if ( ! $kind) {
            return $this->captions;
        }

        return $this->captions->filter(function (Caption $caption) use ($kind) {
            return $caption->getTrackKind() === $kind;
        });
    }

    public function getCaptionIds() {
        return $this->captions->map(function (Caption $caption) {
            return $caption->getYoutubeId();
        });
    }

    /**
     * Set captionsDownloadable.
     *
     * @param bool $captionsDownloadable
     *
     * @return Video
     */
    public function setCaptionsDownloadable($captionsDownloadable) {
        $this->captionsDownloadable = $captionsDownloadable;

        return $this;
    }

    /**
     * Get captionsDownloadable.
     *
     * @return bool
     */
    public function getCaptionsDownloadable() {
        return $this->captionsDownloadable;
    }

    /**
     * Add videoProfile.
     *
     * @return Video
     */
    public function addVideoProfile(VideoProfile $videoProfile) {
        $this->videoProfiles[] = $videoProfile;

        return $this;
    }

    /**
     * Remove videoProfile.
     */
    public function removeVideoProfile(VideoProfile $videoProfile) : void {
        $this->videoProfiles->removeElement($videoProfile);
    }

    /**
     * Get videoProfiles.
     *
     * @return Collection
     */
    public function getVideoProfiles() {
        return $this->videoProfiles;
    }

    public function getVideoProfile(User $user) {
        $profiles = $this->videoProfiles->filter(function (VideoProfile $videoProfile) use ($user) {
            return $videoProfile->getUser() === $user;
        });

        return $profiles->first();
    }

    /**
     * Add screenShot.
     *
     * @param \App\Entity\ScreenShot $screenShot
     *
     * @return Video
     */
    public function addScreenShot(ScreenShot $screenShot) {
        $this->screenShots[] = $screenShot;

        return $this;
    }

    /**
     * Set screen shots.
     *
     * @param Collection|ScreenShot[] $screenShots
     */
    public function setScreenShots($screenShots) : void {
        if ($screenShots instanceof Collection) {
            $this->screenShots = $screenShots;
        } else {
            $this->screenShots = new ArrayCollection($screenShots);
        }
    }

    /**
     * Remove screenShot.
     *
     * @param \App\Entity\ScreenShot $screenShot
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeScreenShot(ScreenShot $screenShot) {
        return $this->screenShots->removeElement($screenShot);
    }

    /**
     * Get screenShots.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getScreenShots() {
        return $this->screenShots;
    }

    /**
     * Set figuration.
     *
     * @param null|\App\Entity\Figuration $figuration
     *
     * @return Video
     */
    public function setFiguration(Figuration $figuration = null) {
        $this->figuration = $figuration;

        return $this;
    }

    /**
     * Get figuration.
     *
     * @return null|\App\Entity\Figuration
     */
    public function getFiguration() {
        return $this->figuration;
    }

    /**
     * Set hidden.
     *
     * @param bool $hidden
     *
     * @return Video
     */
    public function setHidden($hidden) {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * Get hidden.
     *
     * @return bool
     */
    public function getHidden() {
        return $this->hidden;
    }
}
