<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Channel
 *
 * @ORM\Table(name="channel")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ChannelRepository")
 */
class Channel extends AbstractEntity {

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
     * @ORM\Column(type="datetime")
     */
    private $published;

    /**
     * @var Collection|Comment[]
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="channel")
     */
    private $comments;

    /**
     * @var Collection|Comment[]
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
        $this->comments = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->playlists = new ArrayCollection();
    }

    public function __toString() {
        
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Channel
     */
    public function addComment(Comment $comment) {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment) {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments() {
        return $this->comments;
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

}
