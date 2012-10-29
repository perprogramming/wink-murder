<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Entity;

use WinkMurder\Bundle\GameBundle\Tests\WebTestCase;
use WinkMurder\Bundle\GameBundle\Entity\Game;
use WinkMurder\Bundle\GameBundle\Entity\MannerOfDeath;
use WinkMurder\Bundle\GameBundle\Entity\Photo;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;

class MannerOfDeathRepositoryTest extends WebTestCase {

    public function testFindRandomOne() {
        $em = $this->getEntityManager();
        $mannerOfDeathRepository = $em->getRepository('WinkMurderGameBundle:MannerOfDeath');

        $em->persist($photoSet = new PhotoSet('test', 'test'));
        $em->persist($game = new Game($photoSet));

        $numberOfManners = 30;
        $numberOfPlayers = $numberOfManners * 5;

        for ($n = 1; $n <= $numberOfManners; $n++) {
            $manner = new MannerOfDeath();
            $manner->setTranslatableLocale('de');
            $manner->setName($n);
            $manner->setTranslatableLocale('en');
            $manner->setName($n);
            $em->persist($manner);
        }

        $em->flush();

        for ($n = 1; $n <= $numberOfPlayers; $n++) {
            $game->addPlayer(
                $photoSet->addPhoto($n, $n, $n),
                $mannerOfDeathRepository->findRandomOne()
            );
            $em->flush();
        }

        $em->flush();

        $manners = $mannerOfDeathRepository->findAll();

        $max = 0;
        $min = PHP_INT_MAX;
        foreach ($manners as $manner) {
            $numberOfUsages = count($manner->getPlayers());
            if ($max < $numberOfUsages) $max = $numberOfUsages;
            if ($min > $numberOfUsages) $min = $numberOfUsages;
        }

        $this->assertEquals($numberOfManners, count($manners));
        $this->assertEquals($min, ($numberOfPlayers / $numberOfManners));
        $this->assertEquals($max, ($numberOfPlayers / $numberOfManners));
    }

}