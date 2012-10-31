<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Controller;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;

class GuestAccessControllerTest extends WebTestCase {

    public function testLogin() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/guest-access/');
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter('h2:contains("Wähle deinen Spieler")')->count());
    }

    public function testLogoutCheck() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/guest-access/logout/');
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter('h2:contains("Gastzugang")')->count());
    }

    public function testChooseAction() {
        $guestAccessClient = static::createGuestAccessClient();
        $game = static::getCurrentGame();
        $firstUnusedPhoto = reset($game->getUnusedPhotos());
        $guestAccessClient->request('GET', '/guest-access/choose/' . $firstUnusedPhoto->getId());
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter("p.salutation:contains('Hi {$firstUnusedPhoto->getTitle()}!')")->count());
    }

    public function testClearAction() {
        $guestAccessClient = static::createGuestAccessClient();
        $game = static::getCurrentGame();
        $firstUnusedPhoto = reset($game->getUnusedPhotos());
        $guestAccessClient->request('GET', '/guest-access/choose/' . $firstUnusedPhoto->getId());
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter("p.salutation:contains('Hi {$firstUnusedPhoto->getTitle()}!')")->count());
        $guestAccessClient->click($guestAccessClient->getCrawler()->selectLink('Logout')->link());
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter('h2:contains("Wähle deinen Spieler")')->count());
    }

    public function testChooseWithWrongPlayer() {
        $guestAccessClient = static::createGuestAccessClient();
        $guestAccessClient->request('GET', '/guest-access/choose/NAN');
        $this->assertEquals(1, $guestAccessClient->getCrawler()->filter('h2:contains("Wähle deinen Spieler")')->count());
    }

}