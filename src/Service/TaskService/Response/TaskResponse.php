<?php

namespace Xyz\Akulov\Service\TaskService\Response;

use Xyz\Akulov\Service\Core\Response\AbstractResponse;
use Xyz\Akulov\Service\TaskService\Entity\Task;

class TaskResponse extends AbstractResponse
{
    public static function success(Task $result): self
    {
        return new self($result);
    }

    public function getValue(): Task
    {
        return $this->result;
    }
}
