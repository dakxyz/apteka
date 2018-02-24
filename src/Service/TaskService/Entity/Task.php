<?php

namespace Xyz\Akulov\Service\TaskService\Entity;

use DateTime;

class Task
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
     * @var int
     */
    private $id;

    /**
     * @var DateTime
     */
    private $createdAt;

    /**
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @var int
     */
    private $ownerId;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $payload;

    /**
     * @var Step[]
     */
    private $steps = [];

    /**
     * @var string
     */
    private $status;

    public function __construct(int $ownerId, string $type, array $payload)
    {
        $this->ownerId = $ownerId;
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

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }
}
