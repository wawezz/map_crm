<?php

namespace backend\db\repositories;

use backend\db\models\User;

interface UserRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\User[]
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
     * @return \backend\db\models\User[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\User|null
     */
    public function findById(string $id): ?User;

    /**
     * @param string $id
     * @return \backend\db\models\User|null
     */
    public function findBySipID(string $id): ?User;

    /**
     * @param string $email
     * @return \backend\db\models\User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * @param string $email
     * @return int
     */
    public function countByEmail(string $email): int;

    /**
     * @param User $user
     * @return bool
     */
    public function insert(User $user): bool;

    /**
     * @param \backend\db\models\User $user
     * @return bool
     */
    public function update(User $user): bool;

    /**
     * @param array $users
     * @return bool
     */
    public function remove(array $users): bool;

    /**
     * @param \backend\db\models\User $user
     * @return bool
     */
    public function delete(User $user): bool;
}
