<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AccountRepository extends EntityRepository {

    public function findForAdministration() {
        return $this->createDefaultQueryBuilder()->getQuery()->getResult();
    }

    public function findOneByPlayer(Player $player) {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere('p = :player');
        $queryBuilder->setParameter('player', $player);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('a');
        $queryBuilder->join('a.player', 'p');
        $queryBuilder->addOrderBy('p.name', 'ASC');
        return $queryBuilder;
    }

}