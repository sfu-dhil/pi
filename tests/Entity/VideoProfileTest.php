<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\ProfileKeyword;
use App\Entity\VideoProfile;
use PHPUnit\Framework\TestCase;

class VideoProfileTest extends TestCase {
    public function testAddRemoveProfileKeyword() : void {
        $profileKeyword = new ProfileKeyword();

        $profileKeyword->setLabel('new keyword');

        $videoProfile = new VideoProfile();

        $videoProfile->addProfileKeyword($profileKeyword);
        $this->assertCount(1, $videoProfile->getProfileKeywords());
        $this->assertSame('new keyword', $videoProfile->getProfileKeywords()[0]->__toString());

        $videoProfile->removeProfileKeyword($profileKeyword);
        $this->assertCount(0, $videoProfile->getProfileKeywords());
    }
}
