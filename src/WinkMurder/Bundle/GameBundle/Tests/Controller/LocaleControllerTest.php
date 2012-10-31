<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class LocaleControllerTest extends WebTestCase {

    public function testSet() {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/set-locale/en');

        $this->assertEquals(1, $crawler->filter('h1:contains("Winkmurderer still on the loose")')->count());
    }

}