<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/game")
 */
class GameController extends BaseController {

    /**
     * @Route("/")
     */
    public function statusAction() {
        $game = $this->getCurrentGame();
        return $this->render(
            'WinkMurderGameBundle:Game:' . $game->getStatus() . '.html.twig',
            array('game' => $game)
        );
    }

}