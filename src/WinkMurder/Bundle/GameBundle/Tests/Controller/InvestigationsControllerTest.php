<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class ProfileControllerTest extends WebTestCase {

    public function testIndexAction() {
        $game = $this->setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->request('GET', '/investigations/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testIndexAuthenticated() {
        $client = static::createPlayerClient();
        $crawler = $client->getCrawler();
        $spielLink = $crawler->selectLink('Ermittlungen')->link();
        $client->click($spielLink);
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testSuspectAction() {
        $game = static::setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->followRedirects(false);
        $client->request('GET', '/investigations/suspect/1/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testSuspectActionAuthenticated() {
        $game = static::setupGame();
        $murdererClient = static::createPlayerClient($game->getMurdererPhoto());
        $game = $this->getCurrentGame();
        $unusedPhotos = $game->getUnusedPhotos();
        $playerClients = array();
        foreach ($unusedPhotos as $unusedPhoto) {
            $playerClients[] = static::createPlayerClient($unusedPhoto);
        }
        $game = static::getCurrentGame();
        $murderer = $game->getMurderer();
        $players = $game->getOtherPlayers($murderer);

        $murdererClient->request('POST', '/game/commit-murder/' . $players[0]->getId() . '/confirm/');

        foreach ($playerClients as $playerClient) {
            $playerClient->click($playerClient->getCrawler()->selectLink('Ermittlungen')->link());
        }

        $killedPlayerClient = array_shift($playerClients);

        $this->assertEquals(1, $killedPlayerClient->getCrawler()->filter('p:contains("Da du schon tot bist, kannst du nicht mehr an der Jagd auf den Mörder teilnehmen.")')->count());

        foreach($playerClients as $playerClient) {
            $this->assertEquals(1, $playerClient->getCrawler()->filter('h2:contains("Wen verdächtigst du?")')->count());
            $playerClient->click($playerClient->getCrawler()->selectLink($murderer->getName())->link());
        }

        foreach ($playerClients as $playerClient) {
            $playerClient->reload();
            $this->assertEquals(1, $playerClient->getCrawler()->filter("p:contains('Du hattest {$murderer->getName()} verdächtigt.')")->count());
        }
    }

    public function testLikeAction() {
        $game = static::setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->followRedirects(false);
        $client->request('GET', '/investigations/like-murder/1/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

}