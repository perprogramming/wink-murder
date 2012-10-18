<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/murder")
 */
class MurderController extends BaseController {

    /**
     * @Route("/{id}")
     */
    public function commitAction($id) {
        $this->getAuthenticatedPlayer()->murder(
            $this->getCurrentGame()->findPlayer($id)
        );
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
    }

}