<?php

namespace WinkMurder\Bundle\GameBundle\Tests\Entity;

use WinkMurder\Bundle\GameBundle\Entity\Photo;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;

class PhotoTest extends \PHPUnit_Framework_TestCase {

    public function testGetters() {
        $photoSet = new PhotoSet(uniqid(), uniqid());

        $photo = new Photo(
            $photoId = uniqid(),
            $photoSet,
            $photoTitle = uniqid(),
            $photoUrl = uniqid()
        );

        $this->assertEquals($photoId, $photo->getId());
        $this->assertEquals($photoSet, $photo->getPhotoSet());
        $this->assertEquals($photoTitle, $photo->getTitle());
        $this->assertEquals($photoUrl, $photo->getUrl());
    }

}