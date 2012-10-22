<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use WinkMurder\Bundle\GameBundle\Entity\Account;

abstract class BaseController extends Controller {

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
    protected function getAuthenticatedPlayer() {
        if ($account = $this->getAuthenticatedAccount()) {
            return $account->getPlayer();
        }
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Game */
    public function getCurrentGame() {
        return $this->getGameRepository()->findCurrentOne();
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\GameRepository */
    protected function getGameRepository() {
        return $this->getDoctrine()->getRepository('WinkMurderGameBundle:Game');
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\MannerOfDeathRepository */
    protected function getMannerOfDeathRepository() {
        return $this->getDoctrine()->getRepository('WinkMurderGameBundle:MannerOfDeath');
    }

    /** @return \Doctrine\ORM\EntityRepository */
    protected function getPhotoSetRepository() {
        return $this->getDoctrine()->getRepository('WinkMurderGameBundle:PhotoSet');
    }

    protected function getTranslationRepository() {
        return $this->getDoctrine()->getRepository('Stof\DoctrineExtensionsBundle\Entity\Translation');
    }

    /** @return \Doctrine\ORM\EntityManager */
    protected function getEntityManager() {
        return $this->getDoctrine()->getEntityManager();
    }

}