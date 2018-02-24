<?php

namespace Xyz\Akulov\Service\UserService;

use Xyz\Akulov\Service\Core\Response\StringResponse;
use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\UserService\Response\UserResponse;

interface UserServiceInterface
{
    public function registry(string $email, string $password, array $roles = []): VoidResponse;

    public function authorization(string $email, string $password): StringResponse;

    public function getUserByAuthKey(?string $authKey): UserResponse;
}
