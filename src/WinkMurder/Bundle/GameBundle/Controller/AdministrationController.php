<?php

namespace WinkMurder\Bundle\GameBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use WinkMurder\Bundle\GameBundle\Entity\Game;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * @Route("/administration")
 */
class AdministrationController extends BaseController {

    /**
     * @Route("/login/")
     * @Template
     */
    public function loginAction(Request $request) {
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }

    /**
     * @Route("/login/check/")
     */
    public function loginCheckAction() {
    }

    /**
     * @Route("/logout/")
     */
    public function logoutAction() {
    }

    /**
     * @Route("/")
     * @Template
     */
    public function indexAction() {
        return array(
            'game' => $game = $this->getCurrentGame(),
            'photoSets' => $this->getPhotoSetRepository()->findAll(),
            'mannersOfDeath' => $this->getMannerOfDeathRepository()->findAll()
        );
    }

    /**
     * @Route("/select-murderer/")
     */
    public function selectMurdererAction(Request $request) {
        $game = $this->getCurrentGame();
        $game->setMurdererPhoto(
            $game->getPhotoSet()->findPhoto($request->get('id'))
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
     * @Route("/start-game/")
     * @Method("POST")
     */
    public function startGameAction() {
        $game = $this->getCurrentGame();
        $game->start();
        $this->getEntityManager()->flush();
        return $this->redirect($this->generateUrl('winkmurder_game_administration_index'));
    }

    /**
     * @Route("/delete-game/")
     * @Method("POST")
     */
    public function deleteGameAction() {
        $game = $this->getCurrentGame();
        $em = $this->getEntityManager();
        $em->remove($game);
        $em->flush();
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
     * @Route("/create-new-game/")
     * @Method("POST")
     */
    public function createNewGameAction(Request $request) {
        if ($photoSet = $this->getPhotoSetRepository()->find($request->get('id'))) {
            $em = $this->getEntityManager();
            if ($game = $this->getCurrentGame()) {
                $game->finish();
                foreach ($this->getAccountRepository()->findByGame($game) as $account) {
                    $em->remove($account);
                }
            }
            $newGame = new Game(
                $photoSet,
                $this->container->getParameter('duration_of_preliminary_proceedings_in_minutes'),
                $this->container->getParameter('required_positive_suspicion_rate')
            );
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