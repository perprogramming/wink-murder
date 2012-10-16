<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use WinkMurder\Bundle\GameBundle\Security\AccountToken;

class AccountProvider implements AuthenticationProviderInterface {

    protected $userProvider;

    public function __construct(UserProviderInterface $userProvider) {
        $this->userProvider = $userProvider;
    }

    public function authenticate(TokenInterface $token) {
        return new AccountToken($this->userProvider->loadUserByUsername($token->getUsername()));
    }

    public function supports(TokenInterface $token) {
        return $token instanceof AccountToken;
    }

}