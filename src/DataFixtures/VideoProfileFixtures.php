<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\VideoProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\UserBundle\DataFixtures\UserFixtures;

/**
 * LoadVideoProfile form.
 */
class VideoProfileFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
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
            UserFixtures::class,
            VideoFixtures::class,
            ProfileKeywordFixtures::class,
        ];
    }
}
