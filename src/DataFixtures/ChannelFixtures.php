<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Channel;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadChannel form.
 */
class ChannelFixtures extends Fixture implements FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Channel();
            $fixture->setThumbnailUrl('http://example.com/channel/' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setPublishedAt(new DateTimeImmutable());
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new DateTimeImmutable());

            $em->persist($fixture);
            $this->setReference('channel.' . $i, $fixture);
        }

        $em->flush();
    }
}
