<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\VideoProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Nines\UserBundle\DataFixtures\UserFixtures;

/**
 * LoadVideoProfile form.
 */
class VideoProfileFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new VideoProfile();
            $fixture->setUser($this->getReference('user.user'));
            $fixture->setVideo($this->getReference('video.1'));
            $fixture->addProfileKeyword($this->getReference('profilekeyword.1'));
            $em->persist($fixture);
            $this->setReference('videoprofile.' . $i, $fixture);
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
            UserFixtures::class,
            VideoFixtures::class,
            ProfileKeywordFixtures::class,
        ];
    }
}
