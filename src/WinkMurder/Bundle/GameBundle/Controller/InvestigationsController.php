<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/investigations")
 */
class InvestigationsController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        if ($this->getAuthenticatedPlayer()) {
            return array(
                'game' => $this->getCurrentGame()
            );
        }
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

    /**
     * @Route("/suspect/{id}/")
     */
    public function suspectAction($id) {
        if ($this->getAuthenticatedPlayer()) {
            try {
                $this->getAuthenticatedPlayer()->suspect(
                    $this->getCurrentGame()->findPlayer($id)
                );
                $this->getEntityManager()->flush();
            } catch (\Exception $e) {
            }
            return $this->redirect($this->generateUrl('winkmurder_game_investigations_index'));
        }
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

    /**
     * @Route("/like-murder/{id}/")
     */
    public function likeMurderAction($id) {
        if ($this->getAuthenticatedPlayer()) {
            try {
                $this->getAuthenticatedPlayer()->likeMurder(
                    $this->getCurrentGame()->findPlayer($id)
                );
                $this->getEntityManager()->flush();
            } catch (\Exception $e) {
            }
            return $this->redirect($this->generateUrl('winkmurder_game_investigations_index'));
        }
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

}