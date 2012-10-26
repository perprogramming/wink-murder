<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class LocaleController extends BaseController {

    /**
     * @Route("/set-locale/")
     */
    public function setAction(Request $request) {
        $locale = $request->get('locale');
        $request->getSession()->setLocale($locale);
        return $this->redirect($request->headers->get('referer') ?: '/');
    }

}