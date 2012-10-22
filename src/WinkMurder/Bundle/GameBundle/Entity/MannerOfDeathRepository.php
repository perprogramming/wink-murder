<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MannerOfDeathRepository extends EntityRepository {

    public function findRandomOne() {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->addSelect('SIZE(manner.players) as usage');
        $queryBuilder->addOrderBy('usage', 'ASC');
        $result = $queryBuilder->getQuery()->getResult();
        $possibleManners = array();
        $usage = false;
        foreach ($result as $row) {
            if (($usage !== false) && ($usage < $row['usage'])) {
                break;
            }
            $usage = $row['usage'];
            $possibleManners[] = $row['0'];
        }
        return $possibleManners[array_rand($possibleManners)];
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('manner');
        return $queryBuilder;
    }

}