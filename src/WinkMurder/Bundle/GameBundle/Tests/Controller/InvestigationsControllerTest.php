<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class InvestigationsControllerTest extends WebTestCase {

    public function testIndex() {
        $game = $this->setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->request('GET', '/investigations/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testIndexWithGuestAccessWithoutPlayer() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/investigations/');
        $this->assertTrue($guestAccessClient->getResponse()->isForbidden());
    }

    public function testIndexAuthenticated() {
        $client = static::createPlayerClient();
        $crawler = $client->getCrawler();
        $spielLink = $crawler->selectLink('Ermittlungen')->link();
        $client->click($spielLink);
        $this->assertTrue($client->getResponse()->isOk());
    }

    public function testSuspect() {
        $game = static::setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->followRedirects(false);
        $client->request('GET', '/investigations/suspect/1/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testSuspectWithGuestAccessWithoutPlayer() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/investigations/suspect/1/');
        $this->assertTrue($guestAccessClient->getResponse()->isForbidden());
    }

    public function testSuspectAuthenticated() {
        list($murderer, $killedPlayer, $killedPlayerClient, $playerClients) = static::startGameWithMurder();

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

    public function testLike() {
        $game = static::setupGame();
        $client = static::createClient(array(), array('HTTP_HOST' => 'wink-murder.here'));
        $client->followRedirects(false);
        $client->request('GET', '/investigations/like-murder/1/');
        $this->assertTrue($client->getResponse()->isRedirect('http://wink-murder.here/login'));
    }

    public function testLikeWithGuestAccessWithoutPlayer() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/investigations/like-murder/1/');
        $this->assertTrue($guestAccessClient->getResponse()->isForbidden());
    }

    public function testLikeAuthenticated() {
        list($murderer, $killedPlayer, $killedPlayerClient, $playerClients) = static::startGameWithMurder();

        foreach($playerClients as $playerClient) {
            $playerClient->click($playerClient->getCrawler()->selectLink($murderer->getName())->link());
        }

        foreach ($playerClients as $playerClient) {
            $playerClient->request('GET', '/investigations/like-murder/' . $killedPlayer->getId() . '/');
            $this->assertEquals(1, $playerClient->getCrawler()->filter("li:contains('{$killedPlayer->getName()}') .faved")->count());
        }
    }

    protected static function startGameWithMurder() {
        $game = static::setupGame();
        $murdererClient = static::createPlayerClient($game->getMurdererPhoto());
        $game = static::getCurrentGame();
        $unusedPhotos = $game->getUnusedPhotos();
        $playerClients = array();
        foreach ($unusedPhotos as $unusedPhoto) {
            $playerClients[] = static::createPlayerClient($unusedPhoto);
        }
        $game = static::getCurrentGame();
        $murderer = $game->getMurderer();
        $players = $game->getOtherPlayers($murderer);

        $killedPlayer = $players[0];

        $murdererClient->request('POST', '/game/commit-murder/' . $killedPlayer->getId() . '/confirm/');

        foreach ($playerClients as $playerClient) {
            $playerClient->click($playerClient->getCrawler()->selectLink('Ermittlungen')->link());
        }

        $killedPlayerClient = array_shift($playerClients);

        return array($murderer, $killedPlayer, $killedPlayerClient, $playerClients);
    }

}