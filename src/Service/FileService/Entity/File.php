<?php

namespace Xyz\Akulov\Service\FileService\Entity;

use DateTime;

class File
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
    private $name;

    /**
     * @var string
     */
    private $extension;

    /**
     * @var string
     */
    private $hash;

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
    private $sourceFileName;

    /**
     * @var integer
     */
    private $sourceSize;

    /**
     * @var string
     */
    private $sourceMimeType;

    public function __construct(
        string $name,
        string $extension,
        string $hash,
        string $purpose,
        int $ownerId,
        string $sourceFileName,
        string $sourceSize,
        string $sourceMimeType
    ) {
        $this->name = $name;
        $this->extension = $extension;
        $this->hash = $hash;
        $this->purpose = $purpose;
        $this->ownerId = $ownerId;
        $this->sourceFileName = $sourceFileName;
        $this->sourceSize = $sourceSize;
        $this->sourceMimeType = $sourceMimeType;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getPurpose(): string
    {
        return $this->purpose;
    }

    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    public function getSourceFileName(): string
    {
        return $this->sourceFileName;
    }

    public function getSourceSize(): int
    {
        return $this->sourceSize;
    }

    public function getSourceMimeType(): string
    {
        return $this->sourceMimeType;
    }

    public function getPath(?string $root = null): string
    {
        $relativePath = implode(
            '/',
            [
                $this->purpose,
                $this->ownerId,
                $this->name . '.' . $this->extension
            ]
        );

        if ($root) {
            return substr($root, -1) === '/' ?
                $root . $relativePath :
                $root . '/' . $relativePath;
        }

        return $relativePath;
    }

    public function rename(string $newName)
    {
        $this->name = $newName;
    }

    public function compareHash(File $file): bool
    {
        return $this->hash === $file->getHash();
    }
}
