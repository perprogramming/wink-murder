<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WinkMurder\Bundle\GameBundle\Entity\Account;

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
        $user = $this->getSecurityContext()->getToken()->getUser();
        if ($user instanceof Account)
            return $user;
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Player */
    public function getAuthenticatedPlayer() {
        if ($account = $this->getAuthenticatedAccount()) {
            return $account->getPlayer();
        }
    }

}