<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * MetadataElement
 *
 * @ORM\Table(name="metadata_element")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MetadataElementRepository")
 */
class MetadataElement extends AbstractTerm
{
    /**
     * @var Collection|MetadataField[]
     * @ORM\OneToMany(targetEntity="MetadataField", mappedBy="metadataElement")
     */
    private $metadataFields;

    public function __construct() {
        parent::__construct();
        $this->metadataFields = new ArrayCollection();
    }

    /**
     * Add metadataField
     *
     * @param MetadataField $metadataField
     *
     * @return MetadataElement
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
