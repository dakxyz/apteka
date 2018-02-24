<?php

namespace Xyz\Akulov\Symfony\Service\UserService\Security;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Xyz\Akulov\Service\UserService\Entity\User;

class ProxyUser implements UserInterface, EquatableInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $authKey;

    public function __construct(User $user, string $authKey)
    {
        $this->authKey = $authKey;
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getAuthKey(): string
    {
        return $this->authKey;
    }

    /**
     * @throws \Exception
     */
    public function throwExceptionNotAllowedPasswordAuthentication()
    {
        throw new \Exception('not allowed password authentication');
    }

    public function getRoles()
    {
        $mappedRoles = [];

        foreach ($this->user->getRolesAsArray() as $item) {
            $mappedRoles[] = new Role('ROLE_' . strtoupper($item));
        }

        return $mappedRoles;
    }

    /**
     * @return string|void
     * @throws \Exception
     */
    public function getPassword()
    {
        $this->throwExceptionNotAllowedPasswordAuthentication();
    }

    /**
     * @return null|string|void
     * @throws \Exception
     */
    public function getSalt()
    {
        $this->throwExceptionNotAllowedPasswordAuthentication();
    }

    public function getUsername()
    {
        return $this->user->getEmail();
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof ProxyUser) {
            return false;
        }

        if ($this->getUser()->getEmail() !== $user->getUser()->getEmail()) {
            return false;
        }

        if ($this->getUser()->getPasswordHash() !== $user->getUser()->getPasswordHash()) {
            return false;
        }

        return true;
    }
}
