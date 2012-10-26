<?php

namespace WinkMurder\Bundle\GameBundle\Entity\Hash;

class Hash {

    protected $hashable;
    protected $alreadyHashed;

    public function __construct(Hashable $hashable) {
        $this->hashable = $hashable;
        $this->alreadyHashed = new \SplObjectStorage();
    }

    public function __toString() {
        return md5($this->createHash($this->hashable->getHashValues()));
    }

    protected function createHash(array $values) {
        $hashInformation = array();
        foreach ($values as $key => $value) {
            if ($value instanceof Hashable) {
                if ($this->alreadyHashed->contains($value)) {
                    $hash = $value->getId();
                } else {
                    $this->alreadyHashed->attach($value);
                    $hash = $this->createHash($value->getHashValues());
                }
            } elseif (is_array($value)) {
                $hash = $this->createHash($value);
            } elseif ($value instanceof \IteratorAggregate) {
                $hash = $this->createHash(iterator_to_array($value));
            } elseif ($value instanceof \DateTime) {
                $hash = $value->format('U');
            } else {
                $hash = (string) $value;
            }
            $hashInformation[$key] = $hash;
        }
        return implode('-', $hashInformation);
    }

}

