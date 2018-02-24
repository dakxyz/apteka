<?php

namespace Xyz\Akulov\Service\TaskService;

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

    public function createTask(
        ?string $authKey,
        string $type,
        array $payload
    ): TaskResponse;

    public function search(
        ?string $authKey,
        string $type = null,
        string $status = null,
        string $sortByTime = self::SORT_DIRECTION_ASC,
        int $limit = null,
        int $offset = 0
    ): TasksResponse;
}
