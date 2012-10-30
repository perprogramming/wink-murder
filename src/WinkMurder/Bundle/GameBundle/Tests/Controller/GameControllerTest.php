<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class GameControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();

        $client->request('GET', '/game/');

        $this->assertTrue($client->getResponse()->isRedirect('/game/story/'));
    }

    public function testIndexForwardOnStartedGameExists() {
        $this->setupGame();

        $client = static::createClient();

        $client->request('GET', '/game/');

        $this->assertTrue($client->getResponse()->isRedirect('/game/players/'));
    }

    public function testIndexForwardOnStartedGameExistsAuthenticated() {
        $game = $this->setupGame();
        $firstPhoto = reset($game->getUnusedPhotos());

        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/login/');
        $loginLink = $crawler->filter("a:contains('{$firstPhoto->getTitle()}')")->link();
        $crawler = $client->click($loginLink);

        $gameLink = $crawler->filter("a:contains('Spiel')")->link();

        $client->followRedirects(false);
        $client->click($gameLink);

        $this->assertTrue($client->getResponse()->isRedirect('/game/status/'));
    }

}