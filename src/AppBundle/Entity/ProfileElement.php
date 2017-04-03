<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * ProfileElement
 *
 * @ORM\Table(name="profile_element")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProfileElementRepository")
 */
class ProfileElement extends AbstractTerm
{
    /**
     * @var Collection|ProfileField[]
     * @ORM\OneToMany(targetEntity="ProfileField", mappedBy="profileElement")
     */
    private $profileFields;

    public function __construct() {
        parent::__construct();
        $this->profileFields = new ArrayCollection();
    }

    /**
     * Add profileField
     *
     * @param ProfileField $profileField
     *
     * @return ProfileElement
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
}
