<?php

namespace Xyz\Akulov\Service\Core\Response;

class Error
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $validationMessages = [];

    /**
     * @var array
     */
    private $stackTrace = [];

    public function __construct(string $message)
    {
        $this->message = $message;
        $this->stackTrace = debug_backtrace();
    }

    public function addValidationMessage(string $property, string $message)
    {
        $this->validationMessages[$property][] = $message;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getValidationMessages(): array
    {
        return $this->validationMessages;
    }
}
