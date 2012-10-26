<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/refresh")
 */
class RefreshController extends BaseController {

    /**
     * @Route("/")
     */
    public function hashAction() {
        $game = $this->getCurrentGame();
        return new Response(
            $game ? $game->getHash() : md5('no-game'),
            200,
            array('Content-Type' => 'text/plain')
        );
    }

}