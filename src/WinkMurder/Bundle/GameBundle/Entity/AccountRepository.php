<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository {

    public function findByGame(Game $game) {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere('player.game = :game');
        $queryBuilder->setParameter('game', $game);
        return $queryBuilder->getQuery()->getResult();
    }

    public function findByPlayer(Player $player) {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere('player = :player');
        $queryBuilder->setParameter('player', $player);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('account');
        $queryBuilder->join('account.player', 'player');
        $queryBuilder->join('player.photo', 'photo');
        $queryBuilder->addOrderBy('photo.title', 'ASC');
        return $queryBuilder;
    }

}