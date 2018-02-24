<?php

namespace Xyz\Akulov\Service\UserService\Entity;

class Role
{
    const ROOT = 'root';
    const CUSTOMER = 'customer';
    const PHARMACY = 'pharmacy';

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
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
