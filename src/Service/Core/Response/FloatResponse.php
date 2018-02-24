<?php

namespace Xyz\Akulov\Service\Core\Response;

class FloatResponse extends AbstractResponse
{
    public static function success(float $result): self
    {
        return new self($result);
    }

    public function getValue(): float
    {
        return $this->result;
    }
}
