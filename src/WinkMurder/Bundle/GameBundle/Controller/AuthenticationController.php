<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;
use WinkMurder\Bundle\GameBundle\Entity\Account;
use WinkMurder\Bundle\GameBundle\Security\AccountToken;

class AuthenticationController extends BaseController {

    /**
     * @Route("/login/")
     * @Template
     */
    public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'unauthenticatedPlayers' => $this->getPlayerRepository()->findUnauthenticated(),
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login/player/")
     * @Method("POST")
     */
    public function loginPlayerAction($id, Request $request) {
    }

    /**
     * @Route("/login/check/")
     */
    public function loginCheckAction() {
    }

    /**
     * @Route("/logout/")
     */
    public function logoutAction() {
    }

}