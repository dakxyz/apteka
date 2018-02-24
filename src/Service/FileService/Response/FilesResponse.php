<?php

namespace Xyz\Akulov\Service\FileService\Response;

use Xyz\Akulov\Service\Core\Response\AbstractResponse;
use Xyz\Akulov\Service\FileService\Entity\File;

class FilesResponse extends AbstractResponse
{
    public static function success(array $result): self
    {
        return new self($result);
    }

    /**
     * @return File[]
     */
    public function getValue(): array
    {
        return $this->result;
    }
}
