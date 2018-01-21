<?php

namespace Xyz\Akulov\Common\Service\UserService;

use Xyz\Akulov\Common\Entity\User;
use Xyz\Akulov\Common\Service\UserService\Exception\InternalException;
use Xyz\Akulov\Common\Service\UserService\Exception\ValidationException;

interface UserServiceInterface
{
    /**
     * @param string $email
     * @param string $password
     * @param string[] $groups
     * @return void
     *
     * @throws InternalException
     * @throws ValidationException
     */
    public function registry(string $email, string $password, array $groups = []): void;

    /**
     * @param string $email
     * @param string $password
     * @return null|string
     */
    public function authorization(string $email, string $password): ?string;

    /**
     * @param string $authKey
     * @return null|UserService
     */
    public function getUserByAuthKey(string $authKey): ?User;
}
