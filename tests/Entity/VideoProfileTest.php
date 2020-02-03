<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Tests\Entity;

use App\Entity\ProfileKeyword;
use App\Entity\VideoProfile;
use Nines\UtilBundle\Tests\ControllerBaseCase;

class VideoProfileTest extends ControllerBaseCase {
    public function testAddRemoveProfileKeyword() : void {
        $profileKeyword = new ProfileKeyword();

        $profileKeyword->setLabel('new keyword');

        $videoProfile = new VideoProfile();

        $videoProfile->addProfileKeyword($profileKeyword);
        $this->assertSame(1, count($videoProfile->getProfileKeywords()));
        $this->assertSame('new keyword', $videoProfile->getProfileKeywords()[0]);

        $videoProfile->removeProfileKeyword($profileKeyword);
        $this->assertSame(0, count($videoProfile->getProfileKeywords()));
    }
}
