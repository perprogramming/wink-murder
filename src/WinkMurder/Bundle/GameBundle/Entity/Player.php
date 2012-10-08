<?php

namespace WinkMurder\Bundle\GameBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="WinkMurder\Bundle\GameBundle\Entity\PlayerRepository")
 * @ORM\HasLifecycleCallbacks
 * @UniqueEntity(fields={"username"})
 */
class Player implements UserInterface {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $name;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $username;

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    protected $password;

    /**
     * @ORM\Column(nullable=true)
     */
    protected $avatarPath;

    protected $avatarFilenameForRemove;
    protected $avatarFile;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles = array('ROLE_PLAYER');

    /**
     * @ORM\Column(type="datetime")
     */
    protected $lastmod;

    public function __construct() {
        $this->lastmod = new \DateTime();
        $this->password = substr(md5(time()), rand(0, 15), 5);
    }

    public function getId() {
        return $this->id;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getName() {
        return $this->name;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setRoles(array $roles) {
        $this->roles = $roles;
    }

    public function addRole($role) {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function equals(UserInterface $user) {
        return ($this->getUsername() == $user->getUsername()) && ($this->getPassword() == $user->getPassword());
    }

    public function getSalt() {
        return null;
    }

    public function eraseCredentials() {
        $this->password = '';
    }

    public function setAvatarPath($avatarPath) {
        $this->avatarPath = $avatarPath;
    }

    public function getAvatarPath() {
        return $this->avatarPath;
    }

    public function setAvatarFile(\SplFileInfo $avatarFile) {
        $this->avatarFile = $avatarFile;
    }

    public function getAvatarFile() {
        return $this->avatarFile;
    }

    public function setLastmod($lastmod) {
        $this->lastmod = $lastmod;
    }

    public function getLastmod() {
        return $this->lastmod;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload() {
        if (null !== $this->avatarFile) {
            $this->avatarPath = $this->avatarFile->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload() {
        if (null === $this->avatarFile) {
            return;
        }

        $this->avatarFile->move($this->getUploadRootDir(), $this->id . '.' . $this->avatarPath);

        unset($this->avatarFile);
    }

    /**
     * @ORM\PreRemove
     */
    public function storeAvatarFilenameForRemove() {
        $this->avatarFilenameForRemove = $this->getAvatarAbsolutePath();
    }

    /**
     * @ORM\PostRemove
     */
    public function removeAvatarFile() {
        if ($this->avatarFilenameForRemove) {
            unlink($this->avatarFilenameForRemove);
        }
    }

    public function getAvatarUrl() {
        return null === $this->avatarPath ? null : $this->getUploadUrl() . '/' . $this->id . '.' . $this->avatarPath;
    }

    public function getAvatarAbsolutePath() {
        return null === $this->avatarPath ? null : $this->getUploadRootDir() . '/' . $this->id . '.' . $this->avatarPath;
    }

    protected function getUploadRootDir() {
        return __DIR__ . '/../../../../../www/' . $this->getUploadDir();
    }

    protected function getUploadUrl() {
        return '/' . $this->getUploadDir();
    }

    protected function getUploadDir() {
        return 'avatars';
    }

}