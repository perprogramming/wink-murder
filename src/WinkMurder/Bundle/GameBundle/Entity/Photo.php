<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hashable;

/**
 * @ORM\Entity
 */
class Photo implements Hashable {

    /**
     * @ORM\Id
     * @ORM\Column
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="PhotoSet")
     */
    protected $photoSet;

    /**
     * @ORM\Column
     */
    protected $title;

    /**
     * @ORM\Column
     */
    protected $url;

    public function __construct($id, PhotoSet $photoSet, $title, $url) {
        $this->id = $id;
        $this->photoSet = $photoSet;
        $this->title = $title;
        $this->url = $url;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getPhotoSet() {
        return $this->photoSet;
    }

    public function getHashValues() {
        return array(
            'id' => $this->id,
            'photoSet' => $this->photoSet,
            'title' => $this->title,
            'url' => $this->url
        );
    }

}