<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PlayerRepository extends EntityRepository {

    public function findUnauthenticated() {
        return $this->createUnauthenticatedQueryBuilder()->getQuery()->getResult();
    }

    public function findAll() {
        return $this->createDefaultQueryBuilder()->getQuery()->getResult();
    }

    public function findOneUnauthenticated($id) {
        $queryBuilder = $this->createUnauthenticatedQueryBuilder();
        $queryBuilder->andWhere('p.id = :id');
        $queryBuilder->setParameter('id', $id);
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findMurderer() {
        $queryBuilder = $this->createDefaultQueryBuilder();
        $queryBuilder->andWhere('p.murderer = 1');
        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findForIndex(Player $player = null) {
        $queryBuilder = $this->createDefaultQueryBuilder();

        if ($player) {
            $queryBuilder->andWhere('p <> :player');
            $queryBuilder->setParameter('player', $player);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    protected function createUnauthenticatedQueryBuilder() {
        $queryBuilder = $this->createDefaultQueryBuilder();

        $subQueryBuilder = $this->getEntityManager()->createQueryBuilder();
        $subQueryBuilder->select('1');
        $subQueryBuilder->from('WinkMurderGameBundle:Account', 'a');
        $subQueryBuilder->where('a.player = p');

        $queryBuilder->andWhere(
            $subQueryBuilder->expr()->not(
                $queryBuilder->expr()->exists(
                    $subQueryBuilder
                )
            )
        );

        return $queryBuilder;
    }

    protected function createDefaultQueryBuilder() {
        $queryBuilder = $this->createQueryBuilder('p');
        $queryBuilder->addOrderBy('p.name', 'ASC');
        return $queryBuilder;
    }

}