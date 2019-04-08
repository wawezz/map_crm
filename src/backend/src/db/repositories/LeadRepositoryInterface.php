<?php

namespace backend\db\repositories;

use backend\db\models\Lead;

interface LeadRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\Lead[]
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
     * @return \backend\db\models\Lead[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\Lead|null
     */
    public function findById(int $id): ?Lead;

    /**
     * @param Lead $lead
     * @return int
     */

    public function insert(Lead $lead): int;

    /**
     * @param \backend\db\models\Lead $lead
     * @return bool
     */
    public function update(Lead $lead): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function updateByID(int $id): bool;

    /**
     * @param array $leads
     * @return bool
     */
    public function remove(array $leads): bool;

    /**
     * @param \backend\db\models\Lead $lead
     * @return bool
     */
    public function delete(Lead $lead): bool;
}
