<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Channel;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * LoadChannel form.
 */
class LoadChannel extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Channel();
            $fixture->setThumbnailUrl('http://example.com/channel/' . $i);
            $fixture->setTitle('Title ' . $i);
            $fixture->setDescription('Description ' . $i);
            $fixture->setPublishedAt(new DateTime());
            $fixture->setYoutubeId('YoutubeId ' . $i);
            $fixture->setEtag('Etag ' . $i);
            $fixture->setRefreshed(new DateTime());

            $em->persist($fixture);
            $this->setReference('channel.' . $i, $fixture);
        }

        $em->flush();
    }
}
