<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\ProfileKeyword;
use AppBundle\DataFixtures\ORM\LoadProfileKeyword;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class ProfileKeywordControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadProfileKeyword::class
        ];
    }
    
    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_keyword/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testUserIndex() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_keyword/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAdminIndex() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/profile_keyword/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_keyword/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testUserShow() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_keyword/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Back')->count());
    }
    
    public function testAdminShow() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/profile_keyword/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Back')->count());
    }

}
