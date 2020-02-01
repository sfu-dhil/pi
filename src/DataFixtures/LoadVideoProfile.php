<?php

namespace App\DataFixtures\ORM;

use App\Entity\VideoProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;

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
        for($i = 0; $i < 4; $i++) {
            $fixture = new VideoProfile();
            $fixture->setUser($this->getReference('user.user'));
            $fixture->setVideo($this->getReference('video.1'));
            $fixture->addProfileKeyword($this->getReference('profilekeyword.1'));            
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
            LoadUser::class,
            LoadVideo::class,
            LoadProfileKeyword::class,
        ];
    }
    
        
}
