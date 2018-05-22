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
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserIndex() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('New')->count());
    }

    public function testAdminIndex() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/profile_element/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('New')->count());
    }

    public function testAnonShow() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_element/1');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
        
    }

    public function testUserShow() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(0, $crawler->selectLink('Edit')->count());
        
    }

    public function testAdminShow() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $crawler = $client->request('GET', '/profile_element/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->selectLink('Edit')->count());
    }

    public function testAnonEdit() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_element/1/edit');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserEdit() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/1/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminEdit() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $formCrawler = $client->request('GET', '/profile_element/1/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $formCrawler->selectButton('Update')->form([    
            'profile_element[name]' => 'somename',
            'profile_element[label]' => 'SomeName',
            'profile_element[description]' => 'A Description.'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect('/profile_element/1'));
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("somename")')->count());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("SomeName")')->count());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("A Description.")')->count());
    }

    public function testAnonNew() {
        $client = $this->makeClient();
        $crawler = $client->request('GET', '/profile_element/new');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isRedirect());
    }

    public function testUserNew() {
        $client = $this->makeClient(LoadUser::USER);
        $crawler = $client->request('GET', '/profile_element/new');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testAdminNew() {
        $client = $this->makeClient(LoadUser::ADMIN);
        $formCrawler = $client->request('GET', '/profile_element/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
        $form = $formCrawler->selectButton('Create')->form([
                'profile_element[name]' => 'somename',
                'profile_element[label]' => 'SomeName',
                'profile_element[description]' => 'A Description.'
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $responseCrawler = $client->followRedirect();
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("somename")')->count());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("SomeName")')->count());
        $this->assertEquals(1, $responseCrawler->filter('td:contains("A Description.")')->count());
    }

}
