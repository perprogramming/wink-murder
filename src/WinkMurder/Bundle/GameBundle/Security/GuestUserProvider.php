<?php

namespace WinkMurder\Bundle\GameBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class GuestUserProvider implements UserProviderInterface {

    protected $adminPassword;
    protected $entityManager;

    public function __construct($adminPassword, EntityManager $entityManager) {
        $this->adminPassword = $adminPassword;
        $this->entityManager = $entityManager;
    }

    public function loadUserByUsername($username) {
        if ($username == 'admin') {
            return new GuestAccess($this->adminPassword);
        } elseif (preg_match('(guest-access-player:(\d+))', $username, $matches)) {
            return new GuestAccess($this->adminPassword, $this->entityManager->find('WinkMurderGameBundle:Player', $matches[1]));
        }
        throw new UnsupportedUserException("Kein GuestAccess");
    }

    public function refreshUser(UserInterface $user) {
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class == 'WinkMurder\Bundle\GameBundle\Security\GuestAccess';
    }

}