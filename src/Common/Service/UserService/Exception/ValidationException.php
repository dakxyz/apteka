<?php

namespace Xyz\Akulov\Common\Service\UserService\Exception;

use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends Exception
{
    /**
     * @var array
     */
    private $violations = [];

    public function __construct(array $violations)
    {
        $this->violations = $violations;
    }
}
