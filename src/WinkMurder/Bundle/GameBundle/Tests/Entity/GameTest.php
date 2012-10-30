<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Entity;

use WinkMurder\Bundle\GameBundle\Entity\Game;
use WinkMurder\Bundle\GameBundle\Entity\Photo;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;
use WinkMurder\Bundle\GameBundle\Entity\Player;
use WinkMurder\Bundle\GameBundle\Entity\MannerOfDeath;
use WinkMurder\Bundle\GameBundle\Entity\Murder;

class GameTest extends \PHPUnit_Framework_TestCase {

    public function testGetUnusedPhotosSorted() {
        $names = $sortedNames = array('a', 'b', 'c', 'd');
        shuffle($names);
        $this->assertEquals($sortedNames, array_map(function(Photo $photo) {
            return $photo->getTitle();
        }, $this->createGameWithPhotos($names)->getUnusedPhotos()));
    }

    public function testGetPlayersSorted() {
        $names = $sortedNames = array('a', 'b', 'c', 'd');
        shuffle($names);
        $this->assertEquals($sortedNames, array_map(function(Player $player) {
            return $player->getName();
        }, $this->createGameWithPlayers($names)->getPlayers()));
    }

    public function testGetMurdersSorted() {
        $game = $this->createGameWithPlayers(range(1, 10));
        $game->setRequiredMurders(20);

        // Jetzt die Spieler zufällig ermorden
        foreach ($game->getPlayers() as $player) {
            $game->kill($player, null, new \DateTime(date('Y-m-d', rand(0, time()))));
        }

        $timestamps = $sortedTimestamps = array_map(function(Murder $murder) {
            return intval($murder->getTimeOfOffense()->format('U'));
        }, $game->getMurders());

        rsort($sortedTimestamps);

        $this->assertEquals($sortedTimestamps, $timestamps);
    }

    /** @return Game */
    protected function createGameWithPhotos($names) {
        $photoSet = new PhotoSet(uniqid(), uniqid());
        $game = new Game($photoSet);

        foreach ($names as $index => $name) {
            $photoSet->addPhoto(
                $index,
                $name,
                uniqid()
            );
        }

        return $game;
    }

    /** @return Game */
    protected function createGameWithPlayers($names) {
        $mannerOfDeath = new MannerOfDeath();
        $game = $this->createGameWithPhotos($names);

        foreach ($names as $index => $name) {
            $game->addPlayer(
                $game->getPhotoSet()->findPhoto($index),
                $mannerOfDeath
            );
        }

        return $game;
    }

}