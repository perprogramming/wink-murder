<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\SecurityContext;
use WinkMurder\Bundle\GameBundle\Entity\Account;
use Symfony\Component\Security\Core\Authentication\Token\RememberMeToken;

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
     * @Route("/login/player/{id}")
     * @Method("POST")
     */
    public function playerLoginAction($id) {
        if ($player = $this->getPlayerRepository()->findOneUnauthenticated($id)) {
            $account = new Account($player);
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($account);
            $em->flush();

            $this->getSecurityContext()->setToken(new RememberMeToken($account, 'accounts', $account->getUsername()));

            return $this->redirect($this->generateUrl('winkmurder_game_profile_show'));
        } else {
            return $this->redirect($this->generateUrl('winkmurder_game_authentication_login'));
        }
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