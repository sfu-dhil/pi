<?php

namespace App\DataFixtures\ORM;

use AppBundle\Entity\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadKeyword form.
 */
class LoadKeyword extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new Keyword();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");
            
            $em->persist($fixture);
            $this->setReference('keyword.' . $i, $fixture);
        }
        
        $em->flush();
        
    }
        
}
