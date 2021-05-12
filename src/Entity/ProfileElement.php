<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Nines\UtilBundle\Entity\AbstractTerm;

/**
 * ProfileElement.
 *
 * @ORM\Table(name="profile_element")
 * @ORM\Entity(repositoryClass="App\Repository\ProfileElementRepository")
 */
class ProfileElement extends AbstractTerm {
    /**
     * @ORM\OneToMany(targetEntity="ProfileKeyword", mappedBy="profileElement")
     */
    private $profileKeywords;

    public function __construct() {
        parent::__construct();
        $this->profileKeywords = new ArrayCollection();
    }

    /**
     * Add profileKeyword.
     *
     * @return ProfileElement
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
     * @return Collection
     */
    public function getProfileKeywords() {
        return $this->profileKeywords;
    }
}
