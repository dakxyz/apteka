<?php

namespace Xyz\Akulov\Common\Service\UserService;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Ramsey\Uuid\Uuid;
use Redis;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Xyz\Akulov\Common\Entity\Group;
use Xyz\Akulov\Common\Entity\User;
use Xyz\Akulov\Common\Service\UserService\Exception\InternalException;
use Xyz\Akulov\Common\Service\UserService\Exception\ValidationException;

class UserService implements UserServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var Redis
     */
    private $redis;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        Redis $redis
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->redis = $redis;
    }

    /**
     * @inheritdoc
     */
    public function registry(string $email, string $password, array $groups = []): void
    {
        $user = new User($email, hash('sha256', $password));
        $result = $this->validator->validate($user);

        $groups = $this
            ->entityManager
            ->getRepository(Group::class)
            ->findBy(['name' => $groups]);

        foreach ($groups as $group) {
            $user->addToGroup($group);
        }

        if ($result->count() > 0) {
            dump($result); exit();
            throw new ValidationException($result);
        }

        try {
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        } catch (Exception $exception) {
            throw new InternalException($exception->getMessage());
        }
    }

    /**
     * @inheritdoc
     */
    public function authorization(string $email, string $password): ?string
    {
        $user = $this
            ->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            return null;
        }

        if (hash('sha256', $password) !== $user->getPasswordHash()) {
            return null;
        }

        $authKey = Uuid::uuid4()->toString();
        $authKey .= '-' . sprintf('%08x', mt_rand());

        $this->redis->set(
            $this->getRedisKey($authKey),
            $user->getId(),
            24 * 3600 * 360
        );

        return $authKey;
    }

    /**
     * @inheritdoc
     */
    public function getUserByAuthKey(string $authKey): ?User
    {
        return $this->entityManager->getRepository(User::class)->find(
            $this->redis->get($this->getRedisKey($authKey))
        );
    }

    private function getRedisKey($authKey)
    {
        return 'UserService:authKey:' . $authKey;
    }
}
