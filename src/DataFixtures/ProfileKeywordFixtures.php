<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ProfileKeyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadProfileKeyword form.
 */
class ProfileKeywordFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new ProfileKeyword();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");
            $fixture->setProfileElement($this->getReference('profileelement.1'));

            $em->persist($fixture);
            $this->setReference('profilekeyword.' . $i, $fixture);
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
            ProfileElementFixtures::class,
        ];
    }
}
