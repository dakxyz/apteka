<?php

namespace Xyz\Akulov\Service\FileService\Response;

use Xyz\Akulov\Service\Core\Response\AbstractResponse;
use Xyz\Akulov\Service\FileService\Entity\File;

class FileResponse extends AbstractResponse
{
    public static function success(File $result): self
    {
        return new self($result);
    }

    public function getValue(): File
    {
        return $this->result;
    }
}
