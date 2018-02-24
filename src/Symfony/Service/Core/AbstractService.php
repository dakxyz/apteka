<?php

namespace Xyz\Akulov\Symfony\Service\Core;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Xyz\Akulov\Service\Core\Response\Error;

class AbstractService
{
    protected function error(
        string $message,
        ConstraintViolationListInterface $constraintViolationList = null
    ): Error {
        $errorMessage = new Error($message);

        if ($constraintViolationList) {
            /** @var ConstraintViolation $item */
            foreach ($constraintViolationList as $item) {
                $errorMessage->addValidationMessage(
                    $item->getPropertyPath(),
                    $item->getMessage()
                );
            }
        }

        return $errorMessage;
    }

    protected function accessDeniedError()
    {
        return new Error('Доступ запрещен.');
    }
}
