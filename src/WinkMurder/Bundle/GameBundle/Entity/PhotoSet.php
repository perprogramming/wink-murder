<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use WinkMurder\Bundle\GameBundle\Entity\Hash\Hashable;

/**
 * @ORM\Entity
 */
class PhotoSet implements Hashable {

    /**
     * @ORM\Id
     * @ORM\Column
     */
    protected $id;

    /**
     * @ORM\Column
     */
    protected $title;

    /**
     * @ORM\OneToMany(targetEntity="Photo", mappedBy="photoSet", cascade={"PERSIST", "REMOVE"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"})
     */
    protected $photos;

    public function __construct($id, $title) {
        $this->id = $id;
        $this->title = $title;
        $this->photos = new ArrayCollection();
    }

    public function getPhotos() {
        return $this->photos->toArray();
    }

    public function findPhoto($id) {
        foreach ($this->photos as $photo) {
            if ($photo->getId() == $id) {
                return $photo;
            }
        }
    }

    public function addPhoto($id, $title, $url) {
        $photo = new Photo($id, $this, $title, $url);
        $this->photos->add($photo);
        return $photo;
    }

    public function removePhoto(Photo $photo) {
        $this->photos->removeElement($photo);
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getHashValues() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'photos' => $this->photos
        );
    }

}