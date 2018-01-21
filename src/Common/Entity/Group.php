<?php

namespace Xyz\Akulov\Common\Entity;

/**
 * service = UserService
 */
class Group
{
    const ROOT = 'root';
    const CUSTOMER = 'customer';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $group)
    {
        $this->name = $group;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
