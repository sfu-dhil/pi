<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UserBundle\Entity\User;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * VideoProfile
 *
 * @ORM\Table(name="video_profile")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VideoProfileRepository")
 */
class VideoProfile extends AbstractEntity
{

    /**
     * @ORM\ManyToOne(targetEntity="Nines\UserBundle\Entity\User")
     */
    private $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="videoProfiles")
     */
    private $video;

    /**
     * @var Collection|ProfileKeyword[]
     * @ORM\ManyToMany(targetEntity="ProfileKeyword", inversedBy="videos")
     */
    private $profileKeywords; 
    
    public function __construct() {
        parent::__construct();
        $this->profileKeywords = new ArrayCollection();
    }
    
    public function __toString() {
        return (string)$this->video;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return VideoProfile
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add profileKeyword
     *
     * @param ProfileKeyword $profileKeyword
     *
     * @return VideoProfile
     */
    public function addProfileKeyword(ProfileKeyword $profileKeyword)
    {
        $this->profileKeywords[] = $profileKeyword;

        return $this;
    }

    /**
     * Remove profileKeyword
     *
     * @param ProfileKeyword $profileKeyword
     */
    public function removeProfileKeyword(ProfileKeyword $profileKeyword)
    {
        $this->profileKeywords->removeElement($profileKeyword);
    }

    /**
     * Get profileKeywords
     *
     * @return Collection
     */
    public function getProfileKeywords(ProfileElement $profileElement = null)
    {
        if( ! $profileElement) {
            return $this->profileKeywords;
        } 
        return $this->profileKeywords->filter(function(ProfileKeyword $profileKeyword) use($profileElement) {
            return $profileKeyword->getProfileElement() === $profileElement;
        });
    }
    
    public function setProfileKeywords(Collection $profileKeywords) {
        $this->profileKeywords = $profileKeywords;
    }

    /**
     * Set video
     *
     * @param Video $video
     *
     * @return VideoProfile
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
