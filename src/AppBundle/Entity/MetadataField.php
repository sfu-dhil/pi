<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractEntity;

/**
 * MetadataField
 *
 * @ORM\Table(name="metadata_field")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MetadataFieldRepository")
 */
class MetadataField extends AbstractEntity
{
    /**
     * @var MetadataElement
     * @ORM\ManyToOne(targetEntity="MetadataElement", inversedBy="metadataFields")
     */
    private $metadataElement;
    
    /**
     * @var MetadataElement
     * @ORM\ManyToOne(targetEntity="Video", inversedBy="metadataFields")
     */
    private $video;
    
    public function __toString() {
        
    }


    /**
     * Set metadataElement
     *
     * @param MetadataElement $metadataElement
     *
     * @return MetadataField
     */
    public function setMetadataElement(MetadataElement $metadataElement = null)
    {
        $this->metadataElement = $metadataElement;

        return $this;
    }

    /**
     * Get metadataElement
     *
     * @return MetadataElement
     */
    public function getMetadataElement()
    {
        return $this->metadataElement;
    }

    /**
     * Set video
     *
     * @param Video $video
     *
     * @return MetadataField
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
