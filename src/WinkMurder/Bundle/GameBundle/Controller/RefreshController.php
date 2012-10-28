<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/refresh")
 */
class RefreshController extends BaseController {

    /**
     * @Route("/")
     */
    public function hashAction(Request $request) {
        $hashInformation = array();
        $hashInformation['locale'] = $request->getLocale();
        if ($game = $this->getCurrentGame())
            $hashInformation['game'] = $game->getHash();
        if ($this->getAuthenticatedPlayer())
            $hashInformation['authenticated'] = true;

        return new Response(
            md5(implode('-', $hashInformation)),
            200,
            array('Content-Type' => 'text/plain')
        );
    }

}