<?php

namespace Xyz\Akulov\Symfony\Service\TaskService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Xyz\Akulov\Service\Core\Criteria\Criteria;
use Xyz\Akulov\Service\Core\Response\VoidResponse;
use Xyz\Akulov\Service\TaskService\Entity\Step;
use Xyz\Akulov\Service\TaskService\Entity\Task;
use Xyz\Akulov\Service\TaskService\Response\TaskResponse;
use Xyz\Akulov\Service\TaskService\Response\TasksResponse;
use Xyz\Akulov\Service\TaskService\TaskServiceInterface;
use Xyz\Akulov\Service\UserService\UserServiceInterface;
use Xyz\Akulov\Symfony\Service\Core\AbstractService;

class TaskService extends AbstractService implements TaskServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserServiceInterface $usersService,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->userService = $usersService;
        $this->validator = $validator;
    }

    public function createTask(?string $authKey, string $type, array $payload): TaskResponse
    {
        $result = $this->userService->getUserByAuthKey($authKey);
        if (!$result->isSuccess()) {
            return TaskResponse::fail($this->accessDeniedError());
        }

        $user = $result->getValue();

        $task = new Task($user->getId(), $type, $payload);
        $result = $this->validator->validate($task);

        if ($result->count() > 0) {
            return TaskResponse::fail($this->error(
                'Не удалось создать задачу, переданы недопустимые параметры.',
                $result
            ));
        }

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return TaskResponse::success($task);
    }

    public function search(
        ?string $authKey,
        Criteria $criteria
    ): TasksResponse {
        if (!in_array($sortByTime, self::SORT_DIRECTIONS)) {
            return TasksResponse::fail($this->error('Запрос не может быть выполнен.'));
        }

        if ($status && !in_array($status, Task::STATUSES)) {
            return TasksResponse::fail($this->error('Запрос не может быть выполнен.'));
        }

        $result = $this->userService->getUserByAuthKey($authKey);
        if (!$result->isSuccess()) {
            return TasksResponse::fail($this->accessDeniedError());
        }

        $user = $result->getValue();

        $criteria = [
            'ownerId' => $user->getId()
        ];

        if ($type) {
            $criteria['type'] = $type;
        }

        if ($status) {
            $criteria['status'] = $status;
        }

        $tasks = $this
            ->entityManager
            ->getRepository(Task::class)
            ->findBy(
                $criteria,
                ['createdAt' => $sortByTime],
                $limit,
                $offset
            );

        return TasksResponse::success($tasks);
    }

    public function getTaskById(int $taskId): TaskResponse
    {
        $task = $this->entityManager->getRepository(Task::class)->find($taskId);
        if (!$task) {
            return TaskResponse::fail($this->error('Не удалось найти задачу с таким id.'));
        }

        return TaskResponse::success($task);
    }

    public function setTaskStatus(int $taskId, string $status): VoidResponse
    {
        $result = $this->getTaskById($taskId);
        if (!$result->isSuccess()) {
            return VoidResponse::fail($result->getError());
        }

        $task = $result->getValue();
        if (!$task->setStatus($status)) {
            return VoidResponse::fail($this->error('Запрос не может быть выполнен.'));
        }
        $this->entityManager->flush();

        return VoidResponse::success();
    }

    public function addStepToTask(int $taskId, string $type, array $payload): VoidResponse
    {
        $result = $this->getTaskById($taskId);
        if (!$result->isSuccess()) {
            return VoidResponse::fail($result->getError());
        }

        $task = $result->getValue();
        $step = new Step($task, $type, $payload);

        $this->entityManager->persist($step);
        $this->entityManager->flush();

        return VoidResponse::success();
    }
}
