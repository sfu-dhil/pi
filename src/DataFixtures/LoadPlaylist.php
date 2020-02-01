<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Playlist;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadPlaylist form.
 */
class LoadPlaylist extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new Playlist();
            $fixture->setPublishedAt(new \DateTime());
            $fixture->setStatus('Status ' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new \DateTime());
            $fixture->setChannel($this->getReference('channel.1'));
            
            $em->persist($fixture);
            $this->setReference('playlist.' . $i, $fixture);
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
            LoadChannel::class,
        ];
    }
    
        
}
