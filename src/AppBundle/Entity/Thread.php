<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Thread
 *
 * @ORM\Table(name="thread")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThreadRepository")
 */
class Thread extends YoutubeEntity {

    /**
     * The number of replies as reported by the API. Might not be the same as
     * count($this->replies).
     * 
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $replyCount;
    
    /**
     * @var Video
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="threads")
     */
    private $video;

    /**
     * @var Comment
     * @ORM\ManyToOne(targetEntity="Comment")
     */
    private $root;

    /**
     * @var Collection|Comment[]
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="thread")
     */
    private $replies;

    public function __construct() {
        parent::__construct();
    }

    public function __toString() {
        return $this->youtubeId;
    }

    /**
     * Set video
     *
     * @param Video $video
     *
     * @return Thread
     */
    public function setVideo(Video $video = null) {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return Video
     */
    public function getVideo() {
        return $this->video;
    }

    /**
     * Set root
     *
     * @param Comment $root
     *
     * @return Thread
     */
    public function setRoot(Comment $root = null) {
        $this->root = $root;

        return $this;
    }

    /**
     * Get root
     *
     * @return Comment
     */
    public function getRoot() {
        return $this->root;
    }

    /**
     * Add reply
     *
     * @param Comment $reply
     *
     * @return Thread
     */
    public function addReply(Comment $reply) {
        $this->replies[] = $reply;

        return $this;
    }

    /**
     * Remove reply
     *
     * @param Comment $reply
     */
    public function removeReply(Comment $reply) {
        $this->replies->removeElement($reply);
    }

    /**
     * Get replies
     *
     * @return Collection
     */
    public function getReplies() {
        return $this->replies;
    }

    /**
     * Set replyCount
     *
     * @param integer $replyCount
     *
     * @return Thread
     */
    public function setReplyCount($replyCount)
    {
        $this->replyCount = $replyCount;

        return $this;
    }

    /**
     * Get replyCount
     *
     * @return integer
     */
    public function getReplyCount()
    {
        return $this->replyCount;
    }
}
