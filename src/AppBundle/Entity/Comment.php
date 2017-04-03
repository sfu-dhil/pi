<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Comment
 *
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment extends AbstractEntity
{
    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="comments")
     */
    private $channel;
    
    /**
     * @var Video
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="comments")
     */
    private $video;
    
    public function __toString() {
        
    }


    /**
     * Set channel
     *
     * @param Channel $channel
     *
     * @return Comment
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
     * Set video
     *
     * @param Video $video
     *
     * @return Comment
     */
    public function setVideo(Video $video = null)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video
     *
     * @return Video
     */
    public function getVideo()
    {
        return $this->video;
    }
}
