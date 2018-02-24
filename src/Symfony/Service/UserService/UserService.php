<?php

namespace Xyz\Akulov\Symfony\Service\UserService;

use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Redis;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Xyz\Akulov\Service\Core\Response\StringResponse;
use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\UserService\Entity\Role;
use Xyz\Akulov\Service\UserService\Entity\User;
use Xyz\Akulov\Service\UserService\Response\UserResponse;
use Xyz\Akulov\Service\UserService\UserServiceInterface;
use Xyz\Akulov\Symfony\Service\Core\AbstractService;

class UserService extends AbstractService implements UserServiceInterface
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

    public function registry(string $email, string $password, array $roles = []): VoidResponse
    {
        $user = new User($email, hash('sha256', $password));
        $roles = $this
            ->entityManager
            ->getRepository(Role::class)
            ->findBy(['name' => $roles]);

        foreach ($roles as $role) {
            $user->addRole($role);
        }

        $result = $this->validator->validate($user);

        if ($result->count() > 0) {
            return VoidResponse::fail(
                $this->error('Не удалось создать пользователя.', $result)
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return VoidResponse::success();
    }

    public function authorization(string $email, string $password): StringResponse
    {
        $user = $this
            ->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (!$user instanceof User) {
            return StringResponse::fail(
                $this->error('Некоррекртное email или пароль.')
            );
        }

        if (hash('sha256', $password) !== $user->getPasswordHash()) {
            return StringResponse::fail(
                $this->error('Некоррекртное email или пароль.')
            );
        }

        $authKey = Uuid::uuid4()->toString();
        $authKey .= '-' . sprintf('%08x', mt_rand());

        $this->redis->set(
            $this->getRedisKey($authKey),
            $user->getId(),
            24 * 3600 * 360
        );

        return StringResponse::success($authKey);
    }

    public function getUserByAuthKey(?string $authKey): UserResponse
    {
        if (empty($authKey)) {
            return UserResponse::fail($this->error(
                "Некорректный авторизационный токен."
            ));
        }

        $userId = $this->redis->get($this->getRedisKey($authKey));
        if (!$userId) {
            return UserResponse::fail($this->error(
                "Пользователь не авторизован."
            ));
        }

        $user = $this->entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            return UserResponse::fail($this->error('Некорректный авторизационный токен.'));
        }

        return UserResponse::success($user);
    }

    private function getRedisKey($authKey)
    {
        return 'UserService:authKey:' . $authKey;
    }
}
