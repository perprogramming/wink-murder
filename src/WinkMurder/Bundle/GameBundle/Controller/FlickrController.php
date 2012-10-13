<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/flickr")
 */
class FlickrController extends Controller {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array(
            'result' => $this->get('flickr')->photosets_getPhotos(
                $this->container->getParameter('flickr_photoset'),
                'url_m,url_sq'
            )
        );
    }

}