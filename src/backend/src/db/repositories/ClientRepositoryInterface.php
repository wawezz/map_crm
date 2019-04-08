<?php

namespace backend\db\repositories;

use backend\db\models\Client;

interface ClientRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\Client[]
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
     * @return \backend\db\models\Client[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\Client|null
     */
    public function findById(string $id): ?Client;

    /**
     * @param Client $client
     * @return bool
     */

    public function findByPhone(string $phone): ?Client;

    /**
     * @param Client $client
     * @return int
     */

    public function insert(Client $client): int;

    /**
     * @param \backend\db\models\Client $client
     * @return bool
     */
    public function update(Client $client): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function updateByID(int $id): bool;

    /**
     * @param array $clients
     * @return bool
     */
    public function remove(array $clients): bool;

    /**
     * @param \backend\db\models\Client $client
     * @return bool
     */
    public function delete(Client $client): bool;
}
