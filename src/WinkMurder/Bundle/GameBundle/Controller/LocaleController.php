<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class LocaleController extends BaseController {

    /**
     * @Route("/set-locale/{_locale}/")
     * @Method("POST")
     */
    public function setAction(Request $request) {
        return $this->redirect($request->headers->get('referer') ?: '/');
    }

}