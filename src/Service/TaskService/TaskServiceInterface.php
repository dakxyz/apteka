<?php

namespace Xyz\Akulov\Service\TaskService;

use Xyz\Akulov\Service\Core\Criteria\Criteria;
use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\TaskService\Response\TaskResponse;
use Xyz\Akulov\Service\TaskService\Response\TasksResponse;

interface TaskServiceInterface
{
    const
        SORT_DIRECTION_ASC = 'ASC',
        SORT_DIRECTION_DESC = 'DESC';

    const SORT_DIRECTIONS = [
        self::SORT_DIRECTION_ASC,
        self::SORT_DIRECTION_DESC
    ];

    public function createTask(?string $authKey, string $type, array $payload): TaskResponse;

    public function search(?string $authKey, Criteria $criteria): TasksResponse;

    public function getTaskById(int $taskId): TaskResponse;

    public function setTaskStatus(int $taskId, string $status): VoidResponse;

    public function addStepToTask(int $taskId, string $type, array $payload): VoidResponse;
}
