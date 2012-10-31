<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class GameRepository extends EntityRepository {

    public function findCurrentOne() {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere('game.finished = false');
        $queryBuilder->addOrderBy('game.id', 'DESC');
        if ($games = $queryBuilder->getQuery()->getResult()) {
            return reset($games);
        } else {
            return null;
        }
    }

    public function findOneForPlayer(Player $player) {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere(':player MEMBER OF game.players');
        $queryBuilder->setParameter('player', $player);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('game');
        $queryBuilder->join('game.photoSet', 'photoSet');
        $queryBuilder->addSelect('photoSet');
        $queryBuilder->leftJoin('photoSet.photos', 'photo');
        $queryBuilder->addSelect('photo');
        $queryBuilder->leftJoin('game.players', 'player');
        $queryBuilder->addSelect('player');
        $queryBuilder->leftJoin('player.mannerOfDeath', 'mannerOfDeath');
        $queryBuilder->addSelect('mannerOfDeath');
        $queryBuilder->leftJoin('player.account', 'account');
        $queryBuilder->addSelect('account');
        $queryBuilder->leftJoin('game.murders', 'murder');
        $queryBuilder->addSelect('murder');
        $queryBuilder->leftJoin('murder.suspicions', 'suspicion');
        $queryBuilder->addSelect('suspicion');
        return $queryBuilder;
    }

}