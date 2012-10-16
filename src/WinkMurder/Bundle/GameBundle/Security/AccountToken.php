<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use WinkMurder\Bundle\GameBundle\Entity\Account;

class AccountToken extends AbstractToken {

    public function __construct(Account $account) {
        parent::__construct($account->getRoles());
        $this->setUser($account);
        $this->setAuthenticated(true);
    }

    public function getCredentials() {
        return '';
    }

}