<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class ProfileControllerTest extends WebTestCase {

    public function testShow() {
        $game = $this->setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->request('GET', '/you/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testShowWithGuestAccessWithoutPlayer() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/you/');
        $this->assertTrue($guestAccessClient->getResponse()->isForbidden());
    }

    public function testShowAuthenticated() {
        $client = static::createPlayerClient();
        $crawler = $client->getCrawler();
        $spielLink = $crawler->selectLink('Profil')->link();
        $client->click($spielLink);

        $this->assertTrue($client->getResponse()->isOk());
    }

}