<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\DataFixtures;

use App\Entity\Keyword;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * LoadKeyword form.
 */
class KeywordFixtures extends Fixture {
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $em) : void {
        for ($i = 0; $i < 4; $i++) {
            $fixture = new Keyword();
            $fixture->setName("name.{$i}");
            $fixture->setLabel("Name {$i}");

            $em->persist($fixture);
            $this->setReference('keyword.' . $i, $fixture);
        }

        $em->flush();
    }
}
