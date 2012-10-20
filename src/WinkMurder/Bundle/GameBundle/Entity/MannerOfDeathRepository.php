<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MannerOfDeathRepository extends EntityRepository {

    public function findRandomOne() {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $manners = $queryBuilder->getQuery()->getResult();
        $firstManner = reset($manners);
        return $firstManner['0'];
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('manner');
        $queryBuilder->leftJoin('manner.players', 'player');
        $queryBuilder->addSelect('COUNT(player) as usage');
        $queryBuilder->addGroupBy('manner.id');
        $queryBuilder->addOrderBy('usage', 'ASC');
        return $queryBuilder;
    }

}