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

    public function testRules() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/game/rules/');
        $this->assertEquals(1, $crawler->filter('h1:contains("Die Regeln")')->count());
    }

    public function testStory() {
        $client = static::createClient();
        $crawler = $client->request('GET', '/game/story/');
        $this->assertEquals(1, $crawler->filter('h1:contains("Blinzelmörder treibt weiterhin sein Unwesen")')->count());
    }

    public function testPlayers() {
        $game = $this->setupGame();
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/game/players/');
        $this->assertEquals(1, $crawler->filter('h1:contains("Mitspieler")')->count());
        $this->assertEquals(count($game->getPlayers()), $crawler->filter('li.player')->count());
        $this->assertEquals(1, $crawler->selectLink('Jetzt mitspielen')->count());
    }

    public function testPlayersAuthenticated() {
        $game = $this->setupGame();
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/game/players/');
        $loginPageLink = $crawler->selectLink('Jetzt mitspielen')->link();
        $crawler = $client->click($loginPageLink);

        $firstUnusedPhoto = reset($game->getUnusedPhotos());

        $loginLink = $crawler->selectLink($firstUnusedPhoto->getTitle())->link();
        $crawler = $client->click($loginLink);
        $spielLink = $crawler->selectLink('Spiel')->link();
        $crawler = $client->click($spielLink);
        $spielerLink = $crawler->selectLink('Mitspieler')->link();
        $crawler = $client->click($spielerLink);

        $game = $this->getEntityManager()->getRepository('WinkMurderGameBundle:Game')->findCurrentOne();

        $this->assertEquals(0, $crawler->filter('a:contains("Jetzt mitspielen")')->count());
        $this->assertEquals(1, count($game->getPlayers()));
        $this->assertEquals(1, $crawler->filter('li.player')->count());
    }

}