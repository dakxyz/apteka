<?php

namespace Xyz\Akulov\Common\Entity;

/**
 * service = UserService
 */
class User
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $passwordHash;

    /**
     * @var Group[]
     */
    private $groups;

    public function __construct(string $email, string $passwordHash)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
    }

    public function getId(): int
    {
        return $this->id;
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
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    public function addToGroup(Group $group): void
    {
        $this->groups[] = $group;
    }

    public function removeFromGroup(Group $group): void
    {
        foreach ($this->groups as $index => $item) {
            if ($item->getName() === $group->getName()) {
                unset($this->groups[$index]);
                return;
            }
        }
    }

    public function getGroupsAsArray(): array
    {
        $result = [];
        foreach ($this->groups as $index => $item) {
            $result[] = $item->getName();
        }

        return $result;
    }
}
