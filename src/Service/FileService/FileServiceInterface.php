<?php

namespace Xyz\Akulov\Service\FileService;

use Xyz\Akulov\Service\FileService\Response\FileResponse;
use Xyz\Akulov\Service\FileService\Response\FilesResponse;

interface FileServiceInterface
{
    const
        SORT_DIRECTION_ASC = 'ASC',
        SORT_DIRECTION_DESC = 'DESC';

    const SORT_DIRECTIONS = [
        self::SORT_DIRECTION_ASC,
        self::SORT_DIRECTION_DESC
    ];

    public function upload(
        ?string $authKey,
        string $purpose,
        string $pathToTmpFile,
        ?string $sourceFileName = null,
        ?string $sourceFileSize = null,
        ?string $sourceMimeType = null
    ): FileResponse;

    public function search(
        ?string $authKey,
        string $purpose,
        string $sortByTime = self::SORT_DIRECTION_ASC,
        int $limit = null,
        int $offset = 0
    ): FilesResponse;

    public function getFileById(int $fileId): FileResponse;
}
