<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class LocaleController extends BaseController {

    /**
     * @Route("/set-locale/{_locale}")
     */
    public function setAction(Request $request) {
        $this->get('session')->set('_locale', $request->getLocale());
        return $this->redirect($request->headers->get('referer') ?: '/');
    }

}