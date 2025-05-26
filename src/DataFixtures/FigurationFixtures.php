<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Figuration;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadKeyword form.
 */
class FigurationFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Figuration();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");

            $em->persist($fixture);
            $this->setReference('figuration.' . $i, $fixture);
        }

        $em->flush();
    }
}
