<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Figuration;
use AppBundle\Entity\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadKeyword form.
 */
class LoadFiguration extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new Figuration();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");
            
            $em->persist($fixture);
            $this->setReference('figuration.' . $i, $fixture);
        }
        
        $em->flush();
        
    }
        
}
