<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Suspicion {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Murder", inversedBy="suspicions")
     * @ORM\JoinColumn(name="murder_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $murder;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="suspect_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $suspect;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="witness_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $witness;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $timestamp;

    public function __construct(Murder $murder, Player $suspect, Player $witness) {
        $this->murder = $murder;
        $this->suspect = $suspect;
        $this->witness = $witness;
        $this->timestamp = new \DateTime('now');
    }

    public function getId() {
        return $this->id;
    }

    public function getSuspect() {
        return $this->suspect;
    }

    public function getWitness() {
        return $this->witness;
    }

    public function getTimestamp() {
        return $this->timestamp;
    }

    public function isCorrect() {
        return $this->suspect->isMurderer();
    }

}