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

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('game');
        $queryBuilder->join('game.photoSet', 'photoSet');
        $queryBuilder->addSelect('photoSet');
        $queryBuilder->leftJoin('photoSet.photos', 'photo');
        $queryBuilder->addSelect('photo');
        $queryBuilder->leftJoin('game.players', 'player');
        $queryBuilder->addSelect('player');
        return $queryBuilder;
    }

}