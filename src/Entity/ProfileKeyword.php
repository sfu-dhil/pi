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
 * ProfileKeyword.
 *
 * @ORM\Table(name="profile_keyword",
 *  uniqueConstraints={
 *      @ORM\UniqueConstraint(columns={"name", "profile_element_id"})
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ProfileKeywordRepository")
 */
class ProfileKeyword extends AbstractTerm {
    /**
     * @ORM\ManyToOne(targetEntity="ProfileElement", inversedBy="profileKeywords")
     */
    private $profileElement;

    /**
     * @ORM\ManyToMany(targetEntity="VideoProfile", mappedBy="profileKeywords")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
    }

    /**
     * Set profileElement.
     *
     * @param ProfileElement $profileElement
     *
     * @return ProfileKeyword
     */
    public function setProfileElement(ProfileElement $profileElement = null) {
        $this->profileElement = $profileElement;

        return $this;
    }

    /**
     * Get profileElement.
     *
     * @return ProfileElement
     */
    public function getProfileElement() {
        return $this->profileElement;
    }

    /**
     * Add video.
     *
     * @return ProfileKeyword
     */
    public function addVideo(VideoProfile $video) {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video.
     */
    public function removeVideo(VideoProfile $video) : void {
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
}
