<?php

namespace WinkMurder\Bundle\GameBundle\Flickr;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use WinkMurder\Bundle\GameBundle\Entity\PhotoSet;
use WinkMurder\Bundle\GameBundle\Entity\Photo;


class Synchronization {

    protected $flickrService;
    protected $entityManager;
    protected $username;

    public function __construct(\phpFlickr $flickrService, EntityManager $entityManager, $username) {
        $this->flickrService = $flickrService;
        $this->entityManager = $entityManager;
        $this->username = $username;
    }

    public function synchronize() {
        $em = $this->entityManager;
        $photoSetRepository = $this->getPhotoSetRepository();
        $flickr = $this->flickrService;

        // Bestehende PhotoSets und Photos überprüfen
        foreach ($photoSetRepository->findAll() as $photoSet) {
            if (!$flickr->photosets_getInfo($photoSet->getId())) {
                $em->remove($photoSet);
            } else {
                foreach ($photoSet->getPhotos() as $photo) {
                    if (!$flickr->photos_getInfo($photo->getId())) {
                        $photoSet->removePhoto($photo);
                    }
                }
            }
        }

        // Die aktuellen Photos des Users falls nötig erzeugen
        $user = $flickr->people_findByUsername($this->username);
        $photoSetListResult = $flickr->photosets_getList($user['id']);
        foreach ($photoSetListResult['photoset'] as $photoSetData) {
            if (!($photoSet = $photoSetRepository->find($photoSetData['id']))) {
                $photoSet = new PhotoSet($photoSetData['id'], $photoSetData['title']);
                $em->persist($photoSet);
            }
            $photoListResult = $flickr->photosets_getPhotos($photoSet->getId(), 'url_sq');
            if ($photoListResult['stat'] == 'ok') {
                foreach ($photoListResult['photoset']['photo'] as $photoData) {
                    if (!$photoSet->findPhoto($photoData['id'])) {
                        $photoSet->addPhoto($photoData['id'], $photoData['title'], $photoData['url_sq']);
                    }
                }
            }
        }
        $em->flush();
    }

    /** @return \Doctrine\ORM\EntityRepository */
    protected function getPhotoSetRepository() {
        return $this->entityManager->getRepository('WinkMurderGameBundle:PhotoSet');
    }

}