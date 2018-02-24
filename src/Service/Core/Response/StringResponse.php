<?php

namespace Xyz\Akulov\Service\Core\Response;

class StringResponse extends AbstractResponse
{
    public static function success(string $result): self
    {
        return new self($result);
    }

    public function getValue(): string
    {
        return $this->result;
    }
}
