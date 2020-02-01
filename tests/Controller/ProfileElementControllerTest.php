<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\ProfileElement;
use AppBundle\DataFixtures\ORM\LoadProfileElement;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class ProfileElementControllerTest extends BaseTestCase {

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadProfileElement::class
        ];
    }

    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_element/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserIndex() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAdminIndex() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/profile_element/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
   }

    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_element/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserShow() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

}
