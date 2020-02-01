<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Channel;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadChannel form.
 */
class LoadChannel extends Fixture 
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new Channel();
            $fixture->setThumbnailUrl('http://example.com/channel/' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setPublishedAt(new DateTime());
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new DateTime());
            
            $em->persist($fixture);
            $this->setReference('channel.' . $i, $fixture);
        }
        
        $em->flush();
        
    }
    
        
}
