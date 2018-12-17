<?php

namespace Xyz\Akulov\Apteka\Service\PharmacyStockService;

use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\TaskService\Response\TasksResponse;

interface PharmacyStockServiceInterface
{
    const FILE_PURPOSE = 'pharmacy_stock';
    const TASK_PHARMACY_PARSE = 'pharmacy_parse';
    const STEP_GUESS_FORMAT = 'guess_format';

    const
        PAYLOAD_FILE_ID = 'fileId',
        PAYLOAD_NAME = 'name',
        PAYLOAD_EXTENSION = 'extension',
        PAYLOAD_SOURCE_FILE_NAME = 'sourceFileName',
        PAYLOAD_SOURCE_SIZE = 'sourceSize',
        PAYLOAD_SOURCE_MIME_TYPE = 'sourceMimeType';

    public function uploadStockFile(
        ?string $authKey,
        ?string $pathToTmpFile,
        ?string $sourceFileName = null,
        ?string $sourceFileSize = null,
        ?string $sourceMimeType = null
    ): VoidResponse;

    public function getTasks(?string $authKey): TasksResponse;

    public function executeStepGuessFormat(int $taskId): VoidResponse;
}
