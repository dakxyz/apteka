<?php

namespace Xyz\Akulov\Service\Core\Response;

class ArrayResponse extends AbstractResponse
{
    public static function success(array $result): self
    {
        return new self($result);
    }

    public function getValue(): array
    {
        return $this->result;
    }
}
