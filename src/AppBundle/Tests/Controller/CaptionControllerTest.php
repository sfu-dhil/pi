<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Caption;
use AppBundle\DataFixtures\ORM\LoadCaption;
use Nines\UserBundle\DataFixtures\ORM\LoadUser;
use Nines\UtilBundle\Tests\Util\BaseTestCase;

class CaptionControllerTest extends BaseTestCase
{

    protected function getFixtures() {
        return [
            LoadUser::class,
            LoadCaption::class
        ];
    }
    
    public function testAnonIndex() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/caption/');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }
    
    public function testUserIndex() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/caption/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
       
    }
    
    public function testAdminIndex() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/caption/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/caption/1');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }
    
    public function testUserShow() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/caption/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Refresh')->count());
    }
    
    public function testAdminShow() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/caption/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Refresh')->count());
    }

}
