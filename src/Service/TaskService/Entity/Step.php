<?php

namespace Xyz\Akulov\Service\TaskService\Entity;

use DateTime;

class Step
{
    const
        STATUS_NEW = 'new',
        STATUS_IN_PROGRESS = 'in_progress',
        STATUS_FAIL = 'fail',
        STATUS_COMPLETE = 'complete';

    const STATUSES = [
        self::STATUS_NEW,
        self::STATUS_IN_PROGRESS,
        self::STATUS_FAIL,
        self::STATUS_COMPLETE,
    ];

    /**
     * @var integer
     */
    public $id;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @var Task
     */
    private $task;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $payload;

    public function __construct(Task $task, $type, array $payload)
    {
        $this->task = $task;
        $this->type = $type;
        $this->payload = $payload;
        $this->status = self::STATUS_NEW;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getTask(): Task
    {
        return $this->task;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
