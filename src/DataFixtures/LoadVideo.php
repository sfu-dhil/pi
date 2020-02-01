<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Video;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadVideo form.
 */
class LoadVideo extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $em)
    {
        for($i = 0; $i < 4; $i++) {
            $fixture = new Video();
            $fixture->setPublishedAt(new DateTime());
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setThumbnail('Thumbnail ' . $i);
            $fixture->setDuration('P'.($i+1).'M');
            $fixture->setDefinition('Definition ' . $i);
            $fixture->setCaptionsAvailable($i % 2 === 0);
            $fixture->setCaptionsDownloadable($i % 2 === 0);
            $fixture->setLicense('License ' . $i);
            $fixture->setEmbeddable($i % 2 === 0);
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
            $fixture->setFiguration($this->getReference('figuration.'.$i));
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
            LoadChannel::class,
            LoadPlaylist::class,
            LoadKeyword::class,
            LoadFiguration::class,
        ];
    }


}
