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

    public function testLoginCheck() {
        $game = $this->setupGame();

        $firstPhoto = reset($game->getUnusedPhotos());

        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');
        $loginLink = $crawler->filter("a:contains('{$firstPhoto->getTitle()}')")->link();

        $crawler = $client->click($loginLink);
        $response = $client->getResponse();
        $salutation = $crawler->filter('p.salutation');

        $this->assertEquals("Hi {$firstPhoto->getTitle()}!", $salutation->text());
    }

}