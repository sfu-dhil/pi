<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Video;
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
        for($i = 0; $i < 1; $i++) {
            $fixture = new Video();
            $fixture->setPublishedAt('PublishedAt ' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setThumbnail('Thumbnail ' . $i);
            $fixture->setDuration('Duration ' . $i);
            $fixture->setDefinition('Definition ' . $i);
            $fixture->setCaptionsAvailable('CaptionsAvailable ' . $i);
            $fixture->setCaptionsDownloadable('CaptionsDownloadable ' . $i);
            $fixture->setLicense('License ' . $i);
            $fixture->setEmbeddable('Embeddable ' . $i);
            $fixture->setViewCount('ViewCount ' . $i);
            $fixture->setLikeCount('LikeCount ' . $i);
            $fixture->setDislikeCount('DislikeCount ' . $i);
            $fixture->setFavouriteCount('FavouriteCount ' . $i);
            $fixture->setCommentCount('CommentCount ' . $i);
            $fixture->setPlayer('Player ' . $i);
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed('Refreshed ' . $i);
            $fixture->setChannel($this->getReference('channel.1'));
            $fixture->setKeywords($this->getReference('keywords.1'));
            $fixture->setPlaylists($this->getReference('playlists.1'));
            
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
            
        ];
    }
    
        
}
