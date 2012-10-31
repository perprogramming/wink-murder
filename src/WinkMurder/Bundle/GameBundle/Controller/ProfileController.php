<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/you")
 */
class ProfileController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function showAction() {
        if ($this->getAuthenticatedPlayer()) {
            return array('player' => $this->getAuthenticatedPlayer());
        }
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

}