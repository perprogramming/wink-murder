<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/guest-access")
 */
class GuestAccessController extends BaseController {

    /**
     * @Route("/login/")
     * @Template
     */
    public function loginAction(Request $request) {
        if ($this->getAuthenticatedPlayer())
            return $this->redirect($this->generateUrl('winkmurder_game_profile_show'));

        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
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

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array('game' => $this->getCurrentGame());
    }

    /**
     * @Route("/clear/")
     * @Template
     */
    public function clearAction() {
        $guestAccess = $this->getAuthenticatedAccount();
        $guestAccess->setPlayer(null);
        return $this->redirect($this->generateUrl('winkmurder_game_guestaccess_index'));
    }

    /**
     * @Route("/choose/{id}")
     * @Template
     */
    public function chooseAction($id) {
        $guestAccess = $this->getAuthenticatedAccount();
        if ($id) {
            if ($game = $this->getCurrentGame()) {
                if ($photo = $game->getPhotoSet()->findPhoto($id)) {
                    if (!($player = $game->findPlayerByPhoto($photo))) {
                        $player = $game->addPlayer($photo, $this->getMannerOfDeathRepository()->findRandomOne());
                        $this->getEntityManager()->flush();
                    }

                    if (!$player->getAccount()) {
                        $guestAccess->setPlayer($player);
                        return $this->redirect($this->generateUrl('winkmurder_game_profile_show'));
                    }
                }
            }
        } else {
            $guestAccess->setPlayer(null);
        }
        return $this->redirect($this->generateUrl('winkmurder_game_guestaccess_index'));
    }

}