<?php

namespace Xyz\Akulov\Common\Service\FileService;

use Xyz\Akulov\Common\Entity\File;

interface FileServiceInterface
{
    public function upload(
        string $pathToTmpFile,
        string $purpose,
        string $authKey
    ): File;

    public function getUrl(File $file);
}
