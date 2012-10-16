<?php

namespace WinkMurder\Bundle\GameBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Doctrine\Common\Cache\Cache;
use Doctrine\ORM\EntityManager;
use WinkMurder\Bundle\GameBundle\Entity\Player;

class FlickrSynchronization {

    protected $cache;
    protected $flickrService;
    protected $entityManager;
    protected $photosetId;

    public function __construct(Cache $cache, \phpFlickr $flickrService, EntityManager $entityManager, $photosetId) {
        $this->cache = $cache;
        $this->flickrService = $flickrService;
        $this->entityManager = $entityManager;
        $this->photosetId = $photosetId;
    }

    public function onKernelRequest(GetResponseEvent $event) {
        if (HttpKernelInterface::MASTER_REQUEST == $event->getRequestType()) {

            $this->synchronize();

        }
    }

    public function synchronize() {
        if ($photos = $this->getPhotos()) {
            $cacheTimestampIdentifier = 'wink_murder_flickr_cache';
            $cacheTimestamp = intval($this->cache->fetch($cacheTimestampIdentifier));
            $latestUpdate = $this->getLatestUpdate($photos);

            if ($latestUpdate > $cacheTimestamp) {
                $players = $this->getPlayersIndexedByFlickrId();

                foreach ($photos as $photo) {
                    $id = $photo['id'];
                    if (isset($players[$id])) {
                        $player = $players[$id];
                        unset($players[$id]);
                    } else {
                        $player = new Player($id);
                        $this->entityManager->persist($player);
                    }
                    $player->setName($photo['title']);
                    $player->setImageUrl($photo['url_sq']);
                }

                foreach ($players as $player) {
                    if ($account = $this->getAccount($player)) {
                        $this->entityManager->remove($account);
                    }
                    $this->entityManager->remove($player);
                }

                $this->entityManager->flush();
                $this->cache->save($cacheTimestampIdentifier, $latestUpdate);
            }
        }
    }

    protected function getPhotos() {
        $result = $this->flickrService->photosets_getPhotos($this->photosetId, 'url_sq,last_update');

        if ($result['stat'] = 'ok') {
            return $result['photoset']['photo'];
        } else {
            return array();
        }
    }

    protected function getLatestUpdate(array $photos) {
        $latestUpdate = 0;
        foreach ($photos as $photo) {
            $latestUpdate = max($latestUpdate, intval($photo['lastupdate']));
        }
        return $latestUpdate;
    }

    protected function getAccount(Player $player) {
        return $this->entityManager->getRepository('WinkMurderGameBundle:Account')->findOneByPlayer($player);
    }

    protected function getPlayersIndexedByFlickrId() {
        $players = array();
        foreach ($this->entityManager->getRepository('WinkMurderGameBundle:Player')->findAll() as $player) {
            $players[$player->getFlickrId()] = $player;
        }
        return $players;
    }

}