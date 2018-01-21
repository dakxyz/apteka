<?php

namespace Xyz\Akulov\Common\Entity;

/**
 * service = FileService
 */
class File
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $purpose;

    /**
     * @var int
     */
    private $ownerId;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        string $purpose,
        int $ownerId,
        string $name
    ) {
        $this->purpose = $purpose;
        $this->ownerId = $ownerId;
        $this->name = $name;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
