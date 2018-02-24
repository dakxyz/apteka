<?php

namespace Xyz\Akulov\Service\Core\Response;

class VoidResponse extends AbstractResponse
{
    public static function success(): self
    {
        return new self();
    }
}
