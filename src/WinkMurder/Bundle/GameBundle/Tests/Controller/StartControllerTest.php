<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class StartControllerTest extends WebTestCase {

    public function testIndex() {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $this->assertGreaterThan(0, $crawler->filter('h1:contains("Blinzelmörder treibt weiterhin sein Unwesen")')->count());
    }

    public function testIndexForwardOnStartedGameExists() {
        $this->setupGame();

        $client = static::createClient();

        $client->request('GET', '/');
        $response = $client->getResponse();

        $this->assertTrue($response->isRedirect());
        $this->assertEquals('/you/', $response->getTargetUrl());
    }

}