<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Doctrine\ORM\EntityManager;
use WinkMurder\Bundle\GameBundle\Entity\Account;

class LogoutHandler implements \Symfony\Component\Security\Http\Logout\LogoutHandlerInterface {

    protected $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function logout(Request $request, Response $response, TokenInterface $token) {
        $account = $token->getUser();
        if ($account instanceof Account) {
            $this->entityManager->remove($account);
            $this->entityManager->flush();
        }
    }

}