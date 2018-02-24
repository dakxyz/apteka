<?php

namespace Xyz\Akulov\Service\UserService\Response;

use Xyz\Akulov\Service\Core\Response\AbstractResponse;
use Xyz\Akulov\Service\UserService\Entity\User;

class UserResponse extends AbstractResponse
{
    public static function success(User $result): self
    {
        return new self($result);
    }

    public function getValue(): User
    {
        return $this->result;
    }
}
