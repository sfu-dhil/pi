<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadKeyword form.
 */
class LoadKeyword extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 1; $i++) {
            $fixture = new Keyword();
            $fixture->setVideos($this->getReference('videos.1'));
            
            $em->persist($fixture);
            $this->setReference('keyword.' . $i, $fixture);
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
