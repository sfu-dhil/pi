<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Video;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadVideo form.
 */
class VideoFixtures extends Fixture implements DependentFixtureInterface {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Video();
            $fixture->setPublishedAt(new DateTimeImmutable());
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setThumbnail('Thumbnail ' . $i);
            $fixture->setDuration('P' . ($i + 1) . 'M');
            $fixture->setDefinition('Definition ' . $i);
            $fixture->setCaptionsAvailable(0 === $i % 2);
            $fixture->setCaptionsDownloadable(0 === $i % 2);
            $fixture->setLicense('License ' . $i);
            $fixture->setEmbeddable(0 === $i % 2);
            $fixture->setViewCount($i * 100);
            $fixture->setLikeCount($i * 10);
            $fixture->setDislikeCount($i * 5);
            $fixture->setFavouriteCount($i * 4);
            $fixture->setCommentCount($i * 2);
            $fixture->setPlayer('Player ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed();
            $fixture->setChannel($this->getReference('channel.1'));
            $fixture->setFiguration($this->getReference('figuration.' . $i));
            $fixture->addKeyword($this->getReference('keyword.1'));
            $fixture->addPlaylist($this->getReference('playlist.1'));

            $em->persist($fixture);
            $this->setReference('video.' . $i, $fixture);
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
            PlaylistFixtures::class,
            KeywordFixtures::class,
            FigurationFixtures::class,
        ];
    }
}
