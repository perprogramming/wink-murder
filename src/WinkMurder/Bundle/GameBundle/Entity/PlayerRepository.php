<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PlayerRepository extends EntityRepository {

    public function findForIndex() {
        return $this->createDefaultQueryBuilder()->getQuery()->getResult();
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->addOrderBy('p.name', 'ASC');
        return $queryBuilder;
    }

}