<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class AccountController extends BaseController {



    /**
     * @Route("/login/")
     * @Template
     */
    public function loginAction() {
        return array('game' => $this->getCurrentGame());
    }

    /**
     * @Route("/login/check/")
     * @Method("POST")
     */
    public function loginCheckAction() {
    }

    /**
     * @Route("/logout/")
     */
    public function logoutAction() {
    }

}