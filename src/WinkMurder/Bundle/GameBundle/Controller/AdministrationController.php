<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use WinkMurder\Bundle\GameBundle\Entity\UnprivilegedAccount;

/**
 * @Route("/administration")
 */
class AdministrationController extends BaseController {

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array(
            'unauthenticatedPlayers' => $this->getPlayerRepository()->findUnauthenticated(),
            'players' => $this->getPlayerRepository()->findAll(),
            'accounts' => $this->getAccountRepository()->findForAdministration(),
            'murderer' => $this->getPlayerRepository()->findMurderer()
        );
    }

    /**
     * @Route("/select-murderer/")
     * @Method("POST")
     */
    public function selectMurdererAction(Request $request) {
        $players = $this->getPlayerRepository()->findAll();
        foreach ($players as $player) {
            if ($player->getId() == $request->get('id')) {
                $player->setMurderer(true);
            } else {
                $player->setMurderer(false);
            }
        }
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/toggle-dead/{id}")
     * @Method("POST")
     */
    public function toggleDeadAction($id) {
        $player = $this->getPlayerRepository()->find($id);
        $player->setDead(!$player->isDead());
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/unauthenticate/{id}")
     * @Method("POST")
     */
    public function unauthenticateAction($id) {
        $account = $this->getAccountRepository()->find($id);

        if ($account instanceof UnprivilegedAccount) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->remove($account);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/switch-player/{id}")
     * @Method("POST")
     */
    public function switchPlayerAction($id, Request $request) {
        $em = $this->getDoctrine()->getEntityManager();
        $accounts = $this->getAccountRepository()->findAll();
        $account = $this->getAccountRepository()->find($id);
        $oldPlayer = $account->getPlayer();
        $newPlayer = $this->getPlayerRepository()->find($request->get('to'));

        $account->setPlayer(null);
        $em->flush();

        foreach ($accounts as $other) {
            if ($other->getPlayer() === $newPlayer) {
                $other->setPlayer($oldPlayer);
                break;
            }
        }
        $em->flush();

        $account->setPlayer($newPlayer);
        $em->flush();

        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

}