<?php

namespace WinkMurder\Bundle\GameBundle\Entity\Hash;

interface Hashable {

    public function getId();
    public function getHashValues();

}