<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Keyword.
 *
 * @ORM\Table(name="keyword")
 * @ORM\Entity(repositoryClass="App\Repository\KeywordRepository")
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
     * Add video.
     *
     * @return Keyword
     */
    public function addVideo(Video $video) {
        if ( ! $this->hasVideo($video)) {
            $this->videos[] = $video;
        }

        return $this;
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
     * @return Collection|Video[]
     */
    public function getVideos() {
        return $this->videos;
    }

    public function hasVideo(Video $video) {
        return $this->videos->contains($video);
    }
}
