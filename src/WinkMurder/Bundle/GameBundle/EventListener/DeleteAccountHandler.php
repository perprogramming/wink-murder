<?php

namespace WinkMurder\Bundle\GameBundle\EventListener;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;
use WinkMurder\Bundle\GameBundle\Entity\Account;

class DeleteAccountHandler implements \Symfony\Component\Security\Http\Logout\LogoutHandlerInterface {

    protected $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function logout(Request $request, Response $response, TokenInterface $token) {
        if ($token instanceof Account) {
            $this->entityManager->remove($token);
            $this->entityManager->flush();
        }
    }

}