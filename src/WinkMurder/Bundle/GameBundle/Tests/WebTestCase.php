<?php

namespace WinkMurder\Bundle\GameBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Doctrine\ORM\Tools\SchemaTool;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;
use WinkMurder\Bundle\GameBundle\Entity\Photo;
use WinkMurder\Bundle\GameBundle\Entity\Game;
use WinkMurder\Bundle\GameBundle\Entity\MannerOfDeath;

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

    protected function getEntityManager() {
        $container = $this->getContainer();
        $doctrine = $container->get('doctrine');
        return $doctrine->getEntityManager();
    }

    protected function synchronizeWithFlickr() {
        $this->getContainer()->get('wink_murder.flickr_synchronization')->synchronize();
    }

    protected function getContainer() {
        $client = static::createClient();
        return $client->getContainer();
    }

    protected function setupGame() {
        $em = $this->getEntityManager();
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
        return $game;
    }

}