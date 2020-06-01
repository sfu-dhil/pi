<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UserBundle\Entity\User;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * VideoProfile.
 *
 * @ORM\Table(name="video_profile")
 * @ORM\Entity(repositoryClass="App\Repository\VideoProfileRepository")
 */
class VideoProfile extends AbstractEntity {
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Nines\UserBundle\Entity\User")
     */
    private $user;

    /**
     * @var Video
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
        $this->created = new DateTime();
        $this->updated = new DateTime();
    }

    public function __toString() : string {
        return (string) $this->video;
    }

    /**
     * Set user.
     *
     * @param User $user
     *
     * @return VideoProfile
     */
    public function setUser(User $user = null) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Add profileKeyword.
     *
     * @return VideoProfile
     */
    public function addProfileKeyword(ProfileKeyword $profileKeyword) {
        $this->profileKeywords[] = $profileKeyword;

        return $this;
    }

    /**
     * Remove profileKeyword.
     */
    public function removeProfileKeyword(ProfileKeyword $profileKeyword) : void {
        $this->profileKeywords->removeElement($profileKeyword);
    }

    /**
     * Get profileKeywords.
     *
     * @return Collection|ProfileKeyword[]
     */
    public function getProfileKeywords(ProfileElement $profileElement = null) {
        if ( ! $profileElement) {
            return $this->profileKeywords;
        }

        return $this->profileKeywords->filter(function (ProfileKeyword $profileKeyword) use ($profileElement) {
            return $profileKeyword->getProfileElement() === $profileElement;
        });
    }

    public function setProfileKeywords(Collection $profileKeywords) : void {
        $this->profileKeywords = $profileKeywords;
    }

    /**
     * Set video.
     *
     * @param Video $video
     *
     * @return VideoProfile
     */
    public function setVideo(Video $video = null) {
        $this->video = $video;

        return $this;
    }

    /**
     * Get video.
     *
     * @return Video
     */
    public function getVideo() {
        return $this->video;
    }
}
