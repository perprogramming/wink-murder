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
        return array(
            'game' => $this->getCurrentGame()
        );
    }

    /**
     * @Route("/suspect/{id}/")
     */
    public function suspectAction($id) {
        try {
            $this->getAuthenticatedPlayer()->suspect(
                $this->getCurrentGame()->findPlayer($id)
            );
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
        }
        return $this->redirect($this->generateUrl('winkmurder_game_investigations_index'));
    }

    /**
     * @Route("/like-murder/{id}/")
     */
    public function likeMurderAction($id) {
        try {
            $this->getAuthenticatedPlayer()->likeMurder(
                $this->getCurrentGame()->findPlayer($id)
            );
            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
        }
        return $this->redirect($this->generateUrl('winkmurder_game_investigations_index'));
    }

}