<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/investigations/")
 */
class InvestigationsController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array(
            'game' => $this->getCurrentGame()
        );
    }

}