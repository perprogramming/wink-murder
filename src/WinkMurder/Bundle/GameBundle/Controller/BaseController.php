<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller {

    /** @return \WinkMurder\Bundle\GameBundle\Entity\PlayerRepository */
    protected function getPlayerRepository() {
        return $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player');
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\AccountRepository */
    protected function getAccountRepository() {
        return $this->getDoctrine()->getRepository('WinkMurderGameBundle:Account');
    }

    /** @return \Symfony\Component\Security\Core\SecurityContextInterface */
    protected function getSecurityContext() {
        return $this->get('security.context');
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Account */
    protected function getAuthenticatedAccount() {
        return $this->getSecurityContext()->getToken()->getUser();
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Player */
    public function getAuthenticatedPlayer() {
        return $this->getAuthenticatedAccount()->getPlayer();
    }

}