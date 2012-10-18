<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class StartController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $redirectTo = $this->generateUrl('winkmurder_game_administration_index');
        } else {
            $redirectTo = $this->generateUrl('winkmurder_game_profile_show');
        }
        return $this->redirect($redirectTo);
    }

}