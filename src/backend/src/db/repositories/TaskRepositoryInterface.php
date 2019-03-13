<?php

namespace backend\db\repositories;

use backend\db\models\Task;

interface TaskRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\Task[]
     */
    public function findAll(int $limit = null, int $offset = null, array $filter = null, array $sort = null): array;

    /**
     * @param array|null $filter
     * @return int
     */
    public function countAll(array $filter = null): int;

    /**
     * @param string $field
     * @param string $query
     * @param int|null $limit
     * @return \backend\db\models\Task[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\Task|null
     */
    public function findById(string $id): ?Task;

    /**
     * @param Task $task
     * @return int
     */

    public function insert(Task $task): int;

    /**
     * @param \backend\db\models\Task $task
     * @return bool
     */
    public function update(Task $task): bool;

    /**
     * @param array $tasks
     * @return bool
     */
    public function remove(array $tasks): bool;

    /**
     * @param \backend\db\models\Task $task
     * @return bool
     */
    public function delete(Task $task): bool;
}
