<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Caption.
 *
 * @ORM\Table(name="caption")
 * @ORM\Entity(repositoryClass="App\Repository\CaptionRepository")
 */
class Caption extends YoutubeEntity {
    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastUpdated;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $trackKind;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $language;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $audioTrackType;

    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCC;

    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var string
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAutoSynced;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true);
     */
    private $content;

    /**
     * @var Video
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="captions")
     */
    private $video;

    public function __toString() : string {
        if ($this->name) {
            return $this->name;
        }
        if ($this->trackKind && $this->language) {
            return "{$this->trackKind}: {$this->language}";
        }

        return $this->youtubeId;
    }

    /**
     * Set lastUpdated.
     *
     * @param DateTime $lastUpdated
     *
     * @return Caption
     */
    public function setLastUpdated($lastUpdated) {
        $this->lastUpdated = $lastUpdated;

        return $this;
    }

    /**
     * Get lastUpdated.
     *
     * @return DateTime
     */
    public function getLastUpdated() {
        return $this->lastUpdated;
    }

    /**
     * Set trackKind.
     *
     * @param string $trackKind
     *
     * @return Caption
     */
    public function setTrackKind($trackKind) {
        $this->trackKind = $trackKind;

        return $this;
    }

    /**
     * Get trackKind.
     *
     * @return string
     */
    public function getTrackKind() {
        return $this->trackKind;
    }

    /**
     * Set language.
     *
     * @param string $language
     *
     * @return Caption
     */
    public function setLanguage($language) {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage() {
        return $this->language;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Caption
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set audioTrackType.
     *
     * @param string $audioTrackType
     *
     * @return Caption
     */
    public function setAudioTrackType($audioTrackType) {
        $this->audioTrackType = $audioTrackType;

        return $this;
    }

    /**
     * Get audioTrackType.
     *
     * @return string
     */
    public function getAudioTrackType() {
        return $this->audioTrackType;
    }

    /**
     * Set isCC.
     *
     * @param bool $isCC
     *
     * @return Caption
     */
    public function setIsCC($isCC) {
        $this->isCC = $isCC;

        return $this;
    }

    /**
     * Get isCC.
     *
     * @return bool
     */
    public function getIsCC() {
        return $this->isCC;
    }

    /**
     * Set isDraft.
     *
     * @param bool $isDraft
     *
     * @return Caption
     */
    public function setIsDraft($isDraft) {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft.
     *
     * @return bool
     */
    public function getIsDraft() {
        return $this->isDraft;
    }

    /**
     * Set isAutoSynced.
     *
     * @param bool $isAutoSynced
     *
     * @return Caption
     */
    public function setIsAutoSynced($isAutoSynced) {
        $this->isAutoSynced = $isAutoSynced;

        return $this;
    }

    /**
     * Get isAutoSynced.
     *
     * @return bool
     */
    public function getIsAutoSynced() {
        return $this->isAutoSynced;
    }

    /**
     * Set video.
     *
     * @param Video $video
     *
     * @return Caption
     */
    public function setVideo(?Video $video = null) {
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

    /**
     * Set content.
     *
     * @param string $content
     *
     * @return Caption
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }
}
