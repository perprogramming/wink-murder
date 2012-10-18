<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use WinkMurder\Bundle\GameBundle\Entity\Account;
use WinkMurder\Bundle\GameBundle\Entity\Game;

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
            'game' => $game = $this->getCurrentGame(),
            'photoSets' => $this->getPhotoSetRepository()->findAll()
        );
    }

    /**
     * @Route("/select-murderer/")
     */
    public function selectMurdererAction(Request $request) {
        $game = $this->getCurrentGame();
        $game->setMurderer(
            $game->findPlayer($request->get('id'))
        );
        $this->getDoctrine()->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/toggle-dead/{id}/")
     * @Method("POST")
     */
    public function toggleDeadAction($id) {
        $game = $this->getCurrentGame();
        $player = $game->findPlayer($id);
        if ($player->isDead()) {
            $game->resurrect($player);
        } else {
            $game->kill($player);
        }
        $this->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/unauthenticate/{id}/")
     * @Method("POST")
     */
    public function unauthenticateAction($id) {
        if ($player = $this->getCurrentGame()->findPlayer($id)) {
            if ($account = $this->getAccountRepository()->findByPlayer($player)) {
                $em = $this->getEntityManager();
                $em->remove($account->getPlayer());
                $em->remove($account);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/switch-photo/{id}/")
     * @Method("POST")
     */
    public function switchPhotoAction($id, Request $request) {
        $em = $this->getEntityManager();
        $game = $this->getCurrentGame();

        if ($player = $game->findPlayer($id)) {
            if ($newPhoto = $game->getPhotoSet()->findPhoto($request->get('photo'))){
                $oldPhoto = $player->getPhoto();
                foreach ($game->getPlayers() as $otherPlayer) {
                    if ($otherPlayer->getPhoto() === $newPhoto) {
                        $otherPlayer->setPhoto($oldPhoto);
                    }
                }
                $player->setPhoto($newPhoto);
                $em->flush();
            }
        }
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/start-new-game/")
     * @Method("POST")
     */
    public function startNewGameAction(Request $request) {
        if ($photoSet = $this->getPhotoSetRepository()->find($request->get('id'))) {
            $em = $this->getEntityManager();
            if ($game = $this->getCurrentGame()) {
                $game->finish();
                foreach ($this->getAccountRepository()->findByGame($game) as $account) {
                    $em->remove($account);
                }
            }
            $newGame = new Game($photoSet, $this->container->getParameter('duration_of_preliminary_proceedings_in_minutes'));
            $em->persist($newGame);
            $em->flush();
        }
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/synchronize-photosets/")
     * @Method("POST")
     */
    public function synchronizePhotoSetsAction() {
        $this->get('wink_murder.flickr_synchronization')->synchronize();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

}