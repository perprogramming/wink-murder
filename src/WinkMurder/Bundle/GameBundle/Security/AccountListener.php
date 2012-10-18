<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use WinkMurder\Bundle\GameBundle\Security\AccountToken;
use WinkMurder\Bundle\GameBundle\Entity\Account;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityManager;

class AccountListener extends AbstractAuthenticationListener {

    protected $entityManager;

    public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, array $options = array(), AuthenticationSuccessHandlerInterface $successHandler = null, AuthenticationFailureHandlerInterface $failureHandler = null, LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, EntityManager $entityManager) {
        parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $options, $successHandler, $failureHandler, $logger, $dispatcher);
        $this->entityManager = $entityManager;
    }


    protected function attemptAuthentication(Request $request) {
        if ($id = $request->get('id')) {
            $em = $this->entityManager;
            if ($game = $this->getCurrentGame()) {
                if ($photo = $this->getPhoto($id)) {
                    if ($photo->getPhotoSet() === $game->getPhotoSet()) {
                        $player = $game->addPlayer($photo);
                        $account = new Account($player);
                        $em->persist($account);
                        $em->flush();
                        return new AccountToken($account);
                    }
                }
            }
        }
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Game */
    protected function getCurrentGame() {
        return $this->entityManager->getRepository('WinkMurderGameBundle:Game')->findCurrentOne();
    }

    /** @return \WinkMurder\Bundle\GameBundle\Entity\Photo */
    protected function getPhoto($id) {
        return $this->entityManager->getRepository('WinkMurderGameBundle:Photo')->find($id);
    }

}