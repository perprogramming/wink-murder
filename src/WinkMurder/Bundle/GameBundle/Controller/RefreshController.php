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
        return new Response(
            $this->getCurrentGame()->getHash(),
            200,
            array('Content-Type' => 'text/plain')
        );
    }

}