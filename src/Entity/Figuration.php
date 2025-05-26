<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Figuration.
 *
 * @ORM\Table(name="figuration")
 * @ORM\Entity(repositoryClass="App\Repository\FigurationRepository")
 */
class Figuration extends AbstractTerm {
    /**
     * @var Collection|Video[]
     * @ORM\OneToMany(targetEntity="Video", mappedBy="figuration")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
    }

    /**
     * Add video.
     *
     * @param \App\Entity\Video $video
     *
     * @return Figuration
     */
    public function addVideo(Video $video) {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video.
     *
     * @param \App\Entity\Video $video
     *
     * @return bool TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeVideo(Video $video) {
        return $this->videos->removeElement($video);
    }

    /**
     * Get videos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getVideos() {
        return $this->videos;
    }
}
