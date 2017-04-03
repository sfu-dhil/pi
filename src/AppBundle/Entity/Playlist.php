<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * Playlist
 *
 * @ORM\Table(name="playlist")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PlaylistRepository")
 */
class Playlist extends AbstractTerm
{
    /**
     * @var Collection|Video[]
     * @ORM\ManyToMany(targetEntity="Video", inversedBy="playlists")
     */
    private $videos;

    public function __construct() {
        parent::__construct();
        $this->videos= new ArrayCollection();
    }

    /**
     * Add video
     *
     * @param Video $video
     *
     * @return Playlist
     */
    public function addVideo(Video $video)
    {
        $this->videos[] = $video;

        return $this;
    }

    /**
     * Remove video
     *
     * @param Video $video
     */
    public function removeVideo(Video $video)
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
