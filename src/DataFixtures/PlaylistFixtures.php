<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Playlist;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadPlaylist form.
 */
class PlaylistFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Playlist();
            $fixture->setPublishedAt(new DateTimeImmutable());
            $fixture->setStatus('Status ' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new DateTimeImmutable());
            $fixture->setChannel($this->getReference('channel.1'));

            $em->persist($fixture);
            $this->setReference('playlist.' . $i, $fixture);
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
            ChannelFixtures::class,
        ];
    }
}
