<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VideoRepository")
 */
class Video extends AbstractEntity
{

    /**
     * @var Channel
     * @ORM\ManyToOne(targetEntity="Channel", inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $channel;

    /**
     * @var Collection|Comment[]
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="video")
     */
    private $comments;
    
    /**
     * @var Collection|Keyword[]
     * @ORM\ManyToMany(targetEntity="Keyword", inversedBy="videos")
     */
    private $keywords;

    /**
     * @var Collection|Playlist[]
     * @ORM\ManyToMany(targetEntity="Playlist", mappedBy="videos")
     */
    private $playlists;

    /**
     * @var Collection|ProfileField
     * @ORM\OneToMany(targetEntity="ProfileField", mappedBy="video")
     */
    private $profileFields;
    
    /**
     * @var Collection|ProfileField
     * @ORM\OneToMany(targetEntity="MetadataField", mappedBy="video")
     */
    private $metadataFields;
    
    public function __construct() {
        parent::__construct();
        $this->comments = new ArrayCollection();
        $this->keywords = new ArrayCollection();
        $this->playlists = new ArrayCollection();
        $this->profileFields = new ArrayCollection();
        $this->metadataFields = new ArrayCollection();
    }

    public function __toString() {
        
    }


    /**
     * Set channel
     *
     * @param Channel $channel
     *
     * @return Video
     */
    public function setChannel(Channel $channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get channel
     *
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Video
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add keyword
     *
     * @param Keyword $keyword
     *
     * @return Video
     */
    public function addKeyword(Keyword $keyword)
    {
        $this->keywords[] = $keyword;

        return $this;
    }

    /**
     * Remove keyword
     *
     * @param Keyword $keyword
     */
    public function removeKeyword(Keyword $keyword)
    {
        $this->keywords->removeElement($keyword);
    }

    /**
     * Get keywords
     *
     * @return Collection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Add playlist
     *
     * @param Playlist $playlist
     *
     * @return Video
     */
    public function addPlaylist(Playlist $playlist)
    {
        $this->playlists[] = $playlist;

        return $this;
    }

    /**
     * Remove playlist
     *
     * @param Playlist $playlist
     */
    public function removePlaylist(Playlist $playlist)
    {
        $this->playlists->removeElement($playlist);
    }

    /**
     * Get playlists
     *
     * @return Collection
     */
    public function getPlaylists()
    {
        return $this->playlists;
    }

    /**
     * Add profileField
     *
     * @param ProfileField $profileField
     *
     * @return Video
     */
    public function addProfileField(ProfileField $profileField)
    {
        $this->profileFields[] = $profileField;

        return $this;
    }

    /**
     * Remove profileField
     *
     * @param ProfileField $profileField
     */
    public function removeProfileField(ProfileField $profileField)
    {
        $this->profileFields->removeElement($profileField);
    }

    /**
     * Get profileFields
     *
     * @return Collection
     */
    public function getProfileFields()
    {
        return $this->profileFields;
    }

    /**
     * Add metadataField
     *
     * @param MetadataField $metadataField
     *
     * @return Video
     */
    public function addMetadataField(MetadataField $metadataField)
    {
        $this->metadataFields[] = $metadataField;

        return $this;
    }

    /**
     * Remove metadataField
     *
     * @param MetadataField $metadataField
     */
    public function removeMetadataField(MetadataField $metadataField)
    {
        $this->metadataFields->removeElement($metadataField);
    }

    /**
     * Get metadataFields
     *
     * @return Collection
     */
    public function getMetadataFields()
    {
        return $this->metadataFields;
    }
}
