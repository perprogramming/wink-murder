<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/game")
 */
class StatusController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function showAction() {
        return array();
    }

}