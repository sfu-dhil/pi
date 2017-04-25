<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * ProfileKeyword
 *
 * @ORM\Table(name="profile_keyword")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileKeywordRepository")
 */
class ProfileKeyword extends AbstractTerm {

    /**
     * @ORM\ManyToOne(targetEntity="ProfileElement", inversedBy="profileKeywords")
     */
    private $profileElement;
    
    /**
     *
     * @ORM\ManyToMany(targetEntity="VideoProfile", mappedBy="profileKeywords")
     */
    private $videos;
    
    public function __construct() {
        parent::__construct();
        $this->videos = new ArrayCollection();
    }


    /**
     * Set profileElement
     *
     * @param ProfileElement $profileElement
     *
     * @return ProfileKeyword
     */
    public function setProfileElement(ProfileElement $profileElement = null)
    {
        $this->profileElement = $profileElement;

        return $this;
    }

    /**
     * Get profileElement
     *
     * @return ProfileElement
     */
    public function getProfileElement()
    {
        return $this->profileElement;
    }

    /**
     * Add video
     *
     * @param VideoProfile $video
     *
     * @return ProfileKeyword
     */
    public function addVideo(VideoProfile $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param VideoProfile $video
     */
    public function removeVideo(VideoProfile $video)
    {
        $this->videos->removeElement($video);
    }

    /**
     * Get videos
     *
     * @return Collection
     */
    public function getVideos()
    {
        return $this->videos;
    }
}
