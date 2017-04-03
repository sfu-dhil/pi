<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * ProfileField
 *
 * @ORM\Table(name="profile_field")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileFieldRepository")
 */
class ProfileField extends AbstractEntity
{
    /**
     * @var ProfileElement
     * @ORM\ManyToOne(targetEntity="ProfileElement", inversedBy="profileFields")
     */
    private $profileElement;
    
    /**
     * @var ProfileElement
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="profileFields")
     */
    private $video;
    
    public function __toString() {
        
    }


    /**
     * Set profileElement
     *
     * @param ProfileElement $profileElement
     *
     * @return ProfileField
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
     * Set video
     *
     * @param Video $video
     *
     * @return ProfileField
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
