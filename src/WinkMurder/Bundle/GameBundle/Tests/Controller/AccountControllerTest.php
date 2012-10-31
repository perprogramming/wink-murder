<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class AccountControllerTest extends WebTestCase {

    public function testLogin() {
        $game = $this->setupGame();

        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/login/');
        $loginLinks = $crawler->filter('ul.player-list li a');

        $photos = $game->getUnusedPhotos();

        foreach ($photos as $index => $photo) {
            $this->assertEquals($photo->getTitle(), trim($loginLinks->eq($index)->text()));
        }

        $this->assertEquals(count($photos), $loginLinks->count());
    }

    public function testLoginWithoutGame() {
        $client = static::createClient();
        $client->request('GET', '/login/');
        $this->assertTrue($client->getResponse()->isRedirect('/'));
    }

    public function testLoginAuthenticated() {
        $client = static::createPlayerClient();
        $client->followRedirects(false);
        $client->request('GET', '/login/');
        $this->assertTrue($client->getResponse()->isRedirect('/you/'));
    }

    public function testLoginCheck() {
        $client = static::createPlayerClient();
        $crawler = $client->getCrawler();
        $this->assertEquals(1, $crawler->filter('p.salutation')->count());
    }

    public function testUniqueLoginCheck() {
        $game = $this->setupGame();
        $firstPhoto = reset($game->getUnusedPhotos());
        $playerA = static::createPlayerClient($firstPhoto);
        $playerB = static::createPlayerClient($firstPhoto);
        $this->assertEquals(1, $playerA->getCrawler()->filter('p.salutation')->count());
        $this->assertEquals(0, $playerB->getCrawler()->filter('p.salutation')->count());
    }

}