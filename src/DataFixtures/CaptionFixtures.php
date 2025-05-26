<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Caption;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadCaption form.
 */
class CaptionFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface {
    public static function getGroups() : array {
        return ['dev', 'test'];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Caption();
            $fixture->setLastUpdated(new DateTimeImmutable());
            $fixture->setTrackKind('TrackKind ' . $i);
            $fixture->setLanguage('Language ' . $i);
            $fixture->setName('Name ' . $i);
            $fixture->setAudioTrackType('AudioTrackType ' . $i);
            $fixture->setIsCc(false);
            $fixture->setIsDraft(false);
            $fixture->setIsAutoSynced(false);
            $fixture->setContent('Content ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new DateTimeImmutable());
            $fixture->setVideo($this->getReference('video.1'));

            $em->persist($fixture);
            $this->setReference('caption.' . $i, $fixture);
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
            VideoFixtures::class,
        ];
    }
}
