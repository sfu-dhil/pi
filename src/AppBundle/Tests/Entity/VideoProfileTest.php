<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\VideoProfile;
use AppBundle\Entity\ProfileElement;
use AppBundle\Entity\ProfileKeyword;

use Nines\UtilBundle\Tests\Util\BaseTestCase;


class VideoProfileTest extends BaseTestCase{
    

    public function testAddRemoveProfileKeyword() {
        
        $profileKeyword = new ProfileKeyword();
        
        $profileKeyword->setLabel('new keyword');
        
        $videoProfile = new VideoProfile();
        
        $videoProfile->addProfileKeyword($profileKeyword);
        $this->assertEquals(1, count($videoProfile->getProfileKeywords()));
        $this->assertEquals('new keyword', $videoProfile->getProfileKeywords()[0]);
        
        $videoProfile->removeProfileKeyword($profileKeyword);
        $this->assertEquals(0, count($videoProfile->getProfileKeywords()));
    }

    
    
}

