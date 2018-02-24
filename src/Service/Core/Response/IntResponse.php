<?php

namespace Xyz\Akulov\Service\Core\Response;

class IntResponse extends AbstractResponse
{
    public static function success(int $result): self
    {
        return new self($result);
    }

    public function getValue(): int
    {
        return $this->result;
    }
}
