<?php

namespace backend\services\TaskService;

use backend\db\repositories\TaskRepositoryInterface;
use yii\caching\ArrayCache;
use yii\helpers\ArrayHelper;

class TaskService
{
    /**
     * @var \backend\db\repositories\TaskRepositoryInterface
     */
    private $taskRepository;

    /**
     * @var \yii\caching\ArrayCache
     */
    private $cache;

    public function __construct(
        TaskRepositoryInterface $taskRepository
    ) {
        $this->taskRepository = $taskRepository;
        $this->cache = new ArrayCache;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @param array $filter
     * @param array $sort
     * @return \backend\db\models\Task[]
     */
    public function getAllTasks(int $limit, int $offset, array $filter = [], array $sort = []): array
    {
        $total = $this->countAllTasks($filter);
        $maxOffset = $total > 0 ? ($total - 1) : 0;

        $offset = $offset < 0 ? 25 : $offset;
        $offset = $offset > $maxOffset ? $maxOffset : $offset;

        $limit = $limit < 0 ? 25 : $limit;
        $limit = $limit > 1000 ? 1000 : $limit;

        $tasks = $this->taskRepository->findAll($limit, $offset, $filter, $sort);

        $tasksId = ArrayHelper::getColumn($tasks, 'id');

        return $tasks;
    }

    public function countAllTasks(array $filter = null): int
    {
        $result = $this->cache->get(__METHOD__);

        if (!$result) {
            $result = $this->taskRepository->countAll($filter);

            $this->cache->set(__METHOD__, $result);
        }

        return $result;
    }

}
