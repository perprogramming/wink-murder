<?php

namespace WinkMurder\Bundle\GameBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Doctrine\ORM\Tools\SchemaTool;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;
use WinkMurder\Bundle\GameBundle\Entity\Photo;
use WinkMurder\Bundle\GameBundle\Entity\Game;
use WinkMurder\Bundle\GameBundle\Entity\MannerOfDeath;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Client;

class WebTestCase extends BaseWebTestCase {

    protected static function createKernel(array $options = array()) {
        return $GLOBALS['kernel'];
    }

    protected function setUp() {
        $em = $this->getEntityManager();
        $schemaTool = new SchemaTool($em);
        $mdf = $em->getMetadataFactory();
        $classes = $mdf->getAllMetadata();
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    /** @return EntityManager */
    protected static function getEntityManager() {
        $container = static::getContainer();
        $doctrine = $container->get('doctrine');
        return $doctrine->getEntityManager();
    }

    protected function synchronizeWithFlickr() {
        $this->getContainer()->get('wink_murder.flickr_synchronization')->synchronize();
    }

    /** @return ContainerInterface */
    protected static function getContainer() {
        $client = static::createClient();
        return $client->getContainer();
    }

    /** @return Game */
    protected static function setupGame() {
        if (!($game = static::getCurrentGame())) {
            $em = static::getEntityManager();
            $suffocation = new MannerOfDeath();
            $suffocation->setTranslatableLocale('de');
            $suffocation->setName('Ersticken');
            $suffocation->setTranslatableLocale('en');
            $suffocation->setName('Suffocation');
            $em->persist($suffocation);
            $photoSet = new PhotoSet('mock', 'Mock Photoset');
            $photoSet->addPhoto('1', 'Per', 'per.jpg');
            $photoSet->addPhoto('2', 'Søren', 'soeren.jpg');
            $photoSet->addPhoto('3', 'Eva', 'eva.jpg');
            $em->persist($photoSet);
            $game = new Game($photoSet);
            $game->start();
            $em->persist($game);
            $em->flush();
        }
        return $game;
    }

    /** @return Client */
    protected static function createPlayerClient(Photo $photo = null) {
        $game = static::setupGame();
        $client = static::createClient();
        $client->followRedirects();
        $crawler = $client->request('GET', '/game/players/');
        $loginPageLink = $crawler->selectLink('Jetzt mitspielen')->link();
        $crawler = $client->click($loginPageLink);

        if (!$photo) {
            $photo = reset($game->getUnusedPhotos());
        }

        $loginLinkCrawler = $crawler->selectLink($photo->getTitle());
        if ($loginLinkCrawler->count()) {
            $client->click($loginLinkCrawler->link());
        }
        return $client;
    }

    protected static function getCurrentGame() {
        return static::getEntityManager()->getRepository('WinkMurderGameBundle:Game')->findCurrentOne();
    }

}