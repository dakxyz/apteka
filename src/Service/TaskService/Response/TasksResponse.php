<?php

namespace Xyz\Akulov\Service\TaskService\Response;

use Xyz\Akulov\Service\Core\Response\AbstractResponse;
use Xyz\Akulov\Service\TaskService\Entity\Task;

class TasksResponse extends AbstractResponse
{
    public static function success(array $result): self
    {
        return new self($result);
    }

    /**
     * @return Task[]
     */
    public function getValue(): array
    {
        return $this->result;
    }
}
