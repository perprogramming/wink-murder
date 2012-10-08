<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use WinkMurder\Bundle\GameBundle\Form\PlayerType;
use Symfony\Component\HttpFoundation\Request;
use WinkMurder\Bundle\GameBundle\Entity\Player;

/**
 * @Route("/players")
 */
class PlayerController extends Controller {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array('players' => $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player')->findForIndex());
    }

    /**
     * @Route("/edit/{id}/")
     * @Template()
     */
    public function editAction($id) {
        $player = $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player')->find($id);
        $form = $this->createForm(new PlayerType($this->container->getParameter('roleLabels')), $player);

        return array(
            'player' => $player,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/update/{id}/")
     * @Template("WinkMurderGameBundle:Player:edit.html.twig")
     * @Method("POST")
     */
    public function updateAction($id, Request $request) {
        $player = $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player')->find($id);
        $form = $this->createForm(new PlayerType($this->container->getParameter('roleLabels')), $player);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $player->setLastmod(new \DateTime());
            $this->getDoctrine()->getEntityManager()->flush();
            return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
        }

        return array(
            'player' => $player,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/new/")
     * @Template
     */
    public function newAction() {
        $player = new Player();
        $form = $this->createForm(new PlayerType($this->container->getParameter('roleLabels')), $player);

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/create/")
     * @Template("WinkMurderGameBundle:Player:new.html.twig")
     * @Method("POST")
     */
    public function createAction(Request $request) {
        $player = new Player();
        $form = $this->createForm(new PlayerType($this->container->getParameter('roleLabels')), $player);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($player);
            $em->flush();

            return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/delete/{id}")
     * @Method("POST")
     */
    public function deleteAction($id) {
        $player = $this->getDoctrine()->getRepository('WinkMurderGameBundle:Player')->find($id);
        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($player);
        $em->flush();

        return $this->redirect($this->generateUrl('winkmurder_game_player_index'));
    }

}