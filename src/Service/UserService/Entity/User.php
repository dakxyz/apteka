<?php

namespace Xyz\Akulov\Service\UserService\Entity;

use DateTime;

class User
{
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
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var Role[]
     */
    private $roles;

    public function __construct(string $email, string $passwordHash)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role): void
    {
        $this->roles[] = $role;
    }

    public function removeRole(Role $role): void
    {
        foreach ($this->roles as $index => $item) {
            if ($item->getName() === $role->getName()) {
                unset($this->roles[$index]);
                return;
            }
        }
    }

    public function getRolesAsArray(): array
    {
        $result = [];
        foreach ($this->roles as $index => $item) {
            $result[] = $item->getName();
        }

        return $result;
    }

    public function checkAccess(...$roles)
    {
        foreach ($this->roles as $index => $item) {
            if (in_array($item->getName(), $roles)) {
                return true;
            }
        }

        return false;
    }
}
