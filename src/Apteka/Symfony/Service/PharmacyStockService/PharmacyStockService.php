<?php

namespace Xyz\Akulov\Apteka\Symfony\Service\PharmacyStockService;

use Psr\Log\LoggerInterface;
use Xyz\Akulov\Apteka\Service\PharmacyStockService\PharmacyStockServiceInterface;
use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\FileService\FileServiceInterface;
use Xyz\Akulov\Service\TaskService\Entity\Task;
use Xyz\Akulov\Service\TaskService\Response\TasksResponse;
use Xyz\Akulov\Service\TaskService\TaskServiceInterface;
use Xyz\Akulov\Symfony\Service\Core\AbstractService;

class PharmacyStockService extends AbstractService implements PharmacyStockServiceInterface
{
    const MAX_NEW_TASKS = 5;

    /**
     * @var FileServiceInterface
     */
    private $filesService;

    /**
     * @var TaskServiceInterface
     */
    private $tasksService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        FileServiceInterface $filesService,
        TaskServiceInterface $tasksService,
        LoggerInterface $logger
    ) {
        $this->filesService = $filesService;
        $this->tasksService = $tasksService;
        $this->logger = $logger;
    }

    public function uploadStockFile(
        ?string $authKey,
        ?string $pathToTmpFile,
        ?string $sourceFileName = null,
        ?string $sourceFileSize = null,
        ?string $sourceMimeType = null
    ): VoidResponse {
        if (!is_file($pathToTmpFile)) {
            $this->logger->notice('Incorrect path to tmp file', ['path' => $pathToTmpFile]);
            return VoidResponse::fail($this->error('Не удалось загрузить файл.'));
        }

        $result = $this->filesService->search(
            $authKey,
            self::FILE_PURPOSE,
            FileServiceInterface::SORT_DIRECTION_DESC,
            1
        );

        if (!$result->isSuccess()) {
            $this->logger->info('Upload stock file failed', ['error' => $result->getError()]);
            return VoidResponse::fail($this->error('Не удалось загрузить файл.'));
        }

        $files = $result->getValue();
        $checksum = hash_file('sha256', $pathToTmpFile);
        if (count($files) > 0 && $files[0]->getHash() === $checksum) {
            $this->logger->notice('Attempted to upload the stock file twice', ['file' => $files[0]]);
            return VoidResponse::fail($this->error('Этот файл уже загружен и находится в обработке.'));
        }

        $result = $this->tasksService->search(
            $authKey,
            self::TASK_PHARMACY_PARSE,
            Task::STATUS_NEW,
            TaskServiceInterface::SORT_DIRECTION_DESC,
            self::MAX_NEW_TASKS
        );

        if (!$result->isSuccess()) {
            $this->logger->notice('Create task failed', ['error' => $result->getError()]);
            return VoidResponse::fail($this->error('Не удалось загрузить файл.'));
        }

        $tasks = $result->getValue();
        if (count($tasks) >= self::MAX_NEW_TASKS) {
            $this->logger->notice('Max new tasks already in queue', ['error' => $result->getError()]);
            return VoidResponse::fail($this->error('В данный момент в очереди максимальное количество файлов.'));
        }

        $result = $this->filesService->upload(
            $authKey,
            self::FILE_PURPOSE,
            $pathToTmpFile,
            $sourceFileName,
            $sourceFileSize,
            $sourceMimeType
        );

        if (!$result->isSuccess()) {
            $this->logger->notice('Upload stock file failed', ['error' => $result->getError()]);
            return VoidResponse::fail($this->error('Не удалось загрузить файл.'));
        }

        $file = $result->getValue();
        $this->tasksService->createTask(
            $authKey,
            self::TASK_PHARMACY_PARSE,
            [
                self::PAYLOAD_FILE_ID          => $file->getId(),
                self::PAYLOAD_NAME             => $file->getName(),
                self::PAYLOAD_EXTENSION        => $file->getExtension(),
                self::PAYLOAD_SOURCE_FILE_NAME => $file->getSourceFileName(),
                self::PAYLOAD_SOURCE_SIZE      => $file->getSourceSize(),
                self::PAYLOAD_SOURCE_MIME_TYPE => $file->getSourceMimeType(),
            ]
        );
        return VoidResponse::success();
    }

    public function getTasks(?string $authKey): TasksResponse
    {
        return $this->tasksService->search($authKey, self::TASK_PHARMACY_PARSE);
    }

    public function executeStepGuessFormat(int $taskId): VoidResponse
    {
        $result = $this->tasksService->getTaskById($taskId);
        if (!$result->isSuccess()) {
            $this->logger->notice('Unavailable task', [
                'taskId' => $taskId,
                'error'  => $result->getError(),
            ]);

            return VoidResponse::fail($result->getError());
        }

        $task = $result->getValue();
        if ($task->getType() !== self::TASK_PHARMACY_PARSE) {
            $this->logger->notice('Unavailable task type', [
                'task' => $task
            ]);

            return VoidResponse::fail($this->taskFailedError());
        }

        $result = $this->tasksService->addStepToTask($taskId, self::STEP_GUESS_FORMAT, []);
        if (!$result->isSuccess()) {
            return $result;
        }

        $result = $this->tasksService->setTaskStatus($taskId, Task::STATUS_IN_PROGRESS);
        if (!$result->isSuccess()) {
            return $result;
        }

        $payload = $task->getPayload();
        if (!isset($payload[self::PAYLOAD_FILE_ID])) {
            $this->tasksService->setTaskStatus($taskId, Task::STATUS_FAIL);
            $this->logger->notice('Incorrect task, not found fileId in payload', [
                'task' => $task
            ]);
            return VoidResponse::fail($this->taskFailedError());
        }

        $result = $this->filesService->getFileById($payload[self::PAYLOAD_FILE_ID]);
        if (!$result->isSuccess()) {
            $this->tasksService->setTaskStatus($taskId, Task::STATUS_FAIL);
            $this->logger->notice('File not found', [
                'task' => $task
            ]);
            return VoidResponse::fail($result->getError());
        }

        $file = $result->getValue();
        if ($file->getOwnerId() !== $task->getOwnerId()) {
            $this->tasksService->setTaskStatus($taskId, Task::STATUS_FAIL);
            $this->logger->notice('Access denied to file', [
                'task' => $task,
                'file' => $file
            ]);

            return VoidResponse::fail($this->taskFailedError());
        }

        return VoidResponse::success();
    }

    private function taskFailedError()
    {
        return $this->error('Задание не может быть выполнено.');
    }
}
