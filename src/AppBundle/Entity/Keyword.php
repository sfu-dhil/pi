<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Keyword
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\KeywordRepository")
 */
class Keyword extends AbstractTerm {

    /**
     * @var Collection|Video[]
     * @ORM\ManyToMany(targetEntity="Video", mappedBy="keywords")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
    }
    
    public function setName($name) {
        parent::setName($name);
        parent::setLabel($name);
        return $this;
    }

    /**
     * Add video
     *
     * @param Video $video
     *
     * @return Keyword
     */
    public function addVideo(Video $video) {
        if (!$this->hasVideo($video)) {
            $this->videos[] = $video;
        }

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
     * @return Collection|Video[]
     */
    public function getVideos() {
        return $this->videos;
    }

    public function hasVideo(Video $video) {
        return $this->videos->contains($video);
    }
}
