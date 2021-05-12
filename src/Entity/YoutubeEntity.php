<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Description of YoutubeEntity.
 *
 * @author michael
 * @ORM\MappedSuperclass
 * @ORM\Table(
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(columns={"youtube_id"})
 *     }
 * )
 */
abstract class YoutubeEntity extends AbstractEntity {
    /**
     * @var string
     * @ORM\Column(type="string", length=100)
     */
    protected $youtubeId;

    /**
     * @var string
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected $etag;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $refreshed;

    /**
     * Set etag.
     *
     * @param string $etag
     *
     * @return Playlist
     */
    public function setEtag($etag) {
        $this->etag = $etag;

        return $this;
    }

    /**
     * Get etag.
     *
     * @return string
     */
    public function getEtag() {
        return $this->etag;
    }

    /**
     * Set youtubeId.
     *
     * @param string $youtubeId
     *
     * @return Playlist
     */
    public function setYoutubeId($youtubeId) {
        $this->youtubeId = $youtubeId;

        return $this;
    }

    /**
     * Get youtubeId.
     *
     * @return string
     */
    public function getYoutubeId() {
        return $this->youtubeId;
    }

    /**
     * Set refreshed.
     *
     * @param DateTime $refreshed
     *
     * @return Playlist
     */
    public function setRefreshed() {
        $this->refreshed = new DateTimeImmutable();

        return $this;
    }

    /**
     * Get refreshed.
     *
     * @return DateTime
     */
    public function getRefreshed() {
        return $this->refreshed;
    }
}
