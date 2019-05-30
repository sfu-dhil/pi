<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Figuration
 *
 * @ORM\Table(name="figuration")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FigurationRepository")
 */
class Figuration extends AbstractTerm
{
    /**
     * @var Collection|Video[]
     * @ORM\OneToMany(targetEntity="Video", mappedBy="channel")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
    }

    /**
     * Add video.
     *
     * @param \AppBundle\Entity\Video $video
     *
     * @return Figuration
     */
    public function addVideo(\AppBundle\Entity\Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video.
     *
     * @param \AppBundle\Entity\Video $video
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVideo(\AppBundle\Entity\Video $video)
    {
        return $this->videos->removeElement($video);
    }

    /**
     * Get videos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }
}
