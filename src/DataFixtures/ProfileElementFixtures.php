<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\ProfileElement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadProfileElement form.
 */
class ProfileElementFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new ProfileElement();
            $fixture->setName("name_{$i}");
            $fixture->setLabel("Name {$i}");
            $em->persist($fixture);
            $this->setReference('profileelement.' . $i, $fixture);
        }

        $em->flush();
    }
}
