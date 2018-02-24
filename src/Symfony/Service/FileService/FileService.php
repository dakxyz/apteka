<?php

namespace Xyz\Akulov\Symfony\Service\FileService;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Xyz\Akulov\Service\FileService\Entity\File;
use Xyz\Akulov\Service\FileService\FileServiceInterface;
use Xyz\Akulov\Service\FileService\Response\FileResponse;
use Xyz\Akulov\Service\FileService\Response\FilesResponse;
use Xyz\Akulov\Service\UserService\Entity\Role;
use Xyz\Akulov\Service\UserService\UserServiceInterface;
use Xyz\Akulov\Symfony\Service\Core\AbstractService;

class FileService extends AbstractService implements FileServiceInterface
{
    const TIME_FOR_UPLOAD_LIMIT = 24;
    const SIZE_FOR_UPLOAD_LIMIT = 25;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        Filesystem $filesystem,
        UserServiceInterface $usersService,
        ValidatorInterface $validator,
        string $rootPath,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->userService = $usersService;
        $this->validator = $validator;
        $this->rootPath = $rootPath;
        $this->logger = $logger;
    }

    public function upload(
        ?string $authKey,
        string $purpose,
        string $pathToTmpFile,
        ?string $sourceFileName = null,
        ?string $sourceFileSize = null,
        ?string $sourceMimeType = null
    ): FileResponse {
        if (!is_file($pathToTmpFile) || substr($pathToTmpFile, 0, 5) !== '/tmp/') {
            return FileResponse::fail($this->error('Не удалось загрузить файл.'));
        }

        $result = $this->userService->getUserByAuthKey($authKey);
        if (!$result->isSuccess()) {
            return FileResponse::fail($this->accessDeniedError());
        }

        $user = $result->getValue();
        if (!$user->checkAccess(Role::ROOT, Role::PHARMACY)) {
            return FileResponse::fail($this->accessDeniedError());
        }

        $file = new File(
            Uuid::uuid4(),
            pathinfo($sourceFileName, PATHINFO_EXTENSION),
            hash_file('sha256', $pathToTmpFile),
            $purpose,
            $user->getId(),
            $sourceFileName,
            $sourceFileSize,
            $sourceMimeType
        );

        $result = $this->validator->validate($file);
        if ($result->count() > 0) {
            return FileResponse::fail($this->error(
                'Не удалось загрузить файл.',
                $result
            ));
        }

        $size = $this->getSizeLastFilesByTime($user->getId(), self::TIME_FOR_UPLOAD_LIMIT * 60 * 60);
        if ($size >= self::SIZE_FOR_UPLOAD_LIMIT * 1024 * 1024) {
            return FileResponse::fail($this->error(sprintf(
                'Ограничение на загрузку файлов %sМб за %s часа',
                self::SIZE_FOR_UPLOAD_LIMIT,
                self::TIME_FOR_UPLOAD_LIMIT
            )));
        }

        while (!$file->getPath($this->rootPath)) {
            $file->rename(Uuid::uuid4());
        }

        $this->filesystem->copy($pathToTmpFile, $file->getPath($this->rootPath));
        $this->entityManager->persist($file);
        $this->entityManager->flush();

        return FileResponse::success($file);
    }

    public function search(
        ?string $authKey,
        string $purpose,
        string $sortByTime = self::SORT_DIRECTION_ASC,
        int $limit = null,
        int $offset = 0
    ): FilesResponse {
        if (!in_array($sortByTime, self::SORT_DIRECTIONS)) {
            return FilesResponse::fail($this->error('Запрос не может быть выполнен.'));
        }

        $result = $this->userService->getUserByAuthKey($authKey);
        if (!$result->isSuccess()) {
            return FilesResponse::fail($this->accessDeniedError());
        }

        $user = $result->getValue();
        $files = $this
            ->entityManager
            ->getRepository(File::class)
            ->findBy(
                ['purpose' => $purpose, 'ownerId' => $user->getId()],
                ['id' => $sortByTime],
                $limit,
                $offset
            );

        return FilesResponse::success($files);
    }

    private function getSizeLastFilesByTime(int $userId, int $seconds): int
    {
        $sql = <<<SQL
SELECT SUM(source_size) AS size
FROM files
WHERE owner_id = :owner_id AND created_at >= :time
SQL;

        $connection = $this->entityManager->getConnection();
        $stmt = $connection->prepare($sql);

        $now = new \DateTime();
        $now->modify("-{$seconds} seconds");

        $stmt->execute([
            'owner_id' => $userId,
            'time'     => $now->format('Y-m-d H:i:s')
        ]);

        return intval($stmt->fetch()['size'] ?? 0);
    }
}
