<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("players/")
 */
class PlayerController extends Controller {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array('players' => $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player')->findForIndex());
    }

}