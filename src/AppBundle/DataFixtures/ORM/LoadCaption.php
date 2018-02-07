<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Caption;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadCaption form.
 */
class LoadCaption extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 1; $i++) {
            $fixture = new Caption();
            $fixture->setLastUpdated('LastUpdated ' . $i);
            $fixture->setTrackKind('TrackKind ' . $i);
            $fixture->setLanguage('Language ' . $i);
            $fixture->setName('Name ' . $i);
            $fixture->setAudioTrackType('AudioTrackType ' . $i);
            $fixture->setIsCc('IsCc ' . $i);
            $fixture->setIsDraft('IsDraft ' . $i);
            $fixture->setIsAutoSynced('IsAutoSynced ' . $i);
            $fixture->setContent('Content ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed('Refreshed ' . $i);
            $fixture->setVideo($this->getReference('video.1'));
            
            $em->persist($fixture);
            $this->setReference('caption.' . $i, $fixture);
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
