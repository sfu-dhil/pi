<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\VideoProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadVideoProfile form.
 */
class LoadVideoProfile extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 1; $i++) {
            $fixture = new VideoProfile();
            $fixture->setUser($this->getReference('user.1'));
            $fixture->setVideo($this->getReference('video.1'));
            $fixture->setProfilekeywords($this->getReference('profileKeywords.1'));
            
            $em->persist($fixture);
            $this->setReference('videoprofile.' . $i, $fixture);
        }
        
        $em->flush();
        
    }
    
    /**
     * {@inheritdoc}
     */
    public function getDependencies() {
        // add dependencies here, or remove this 
        // function and "implements DependentFixtureInterface" above
        return [
            
        ];
    }
    
        
}
