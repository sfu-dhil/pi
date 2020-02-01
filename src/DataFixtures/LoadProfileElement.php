<?php

namespace App\DataFixtures\ORM;

use App\Entity\ProfileElement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadProfileElement form.
 */
class LoadProfileElement extends Fixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new ProfileElement();
            $fixture->setName("name_{$i}");
            $fixture->setLabel("Name {$i}");
            $em->persist($fixture);
            $this->setReference('profileelement.' . $i, $fixture);
        }
        
        $em->flush();
        
    }    
        
}
