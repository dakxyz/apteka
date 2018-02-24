<?php

namespace Xyz\Akulov\Service\Core\Response;

abstract class AbstractResponse
{
    /**
     * @var Error|null
     */
    protected $error;

    /**
     * @var mixed
     */
    protected $result;

    protected function __construct($result = null)
    {
        $this->result = $result;
    }

    public function isSuccess(): bool
    {
        return $this->error === null;
    }

    public static function fail(Error $errorMessage)
    {
        $response = new static();
        $response->error = $errorMessage;
        return $response;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }
}
