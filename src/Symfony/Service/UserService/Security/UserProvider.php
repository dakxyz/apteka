<?php

namespace Xyz\Akulov\Symfony\Service\UserService\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Xyz\Akulov\Service\UserService\Entity\User;
use Xyz\Akulov\Service\UserService\UserServiceInterface;

class UserProvider implements UserProviderInterface
{
    /**
     * @var UserServiceInterface
     */
    private $usersService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->usersService = $userService;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->usersService->getUserByAuthKey($username);

        if ($user->isSuccess() && $user->getValue() instanceof User) {
            return new ProxyUser($user->getValue(), $username);
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof ProxyUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getAuthKey());
    }

    public function supportsClass($class)
    {
        return ProxyUser::class === $class;
    }
}
