<?php

namespace backend\db\repositories;

use backend\db\models\PlaceLead;

interface PlaceLeadRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\PlaceLead[]
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
     * @return \backend\db\models\PlaceLead[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\PlaceLead|null
     */
    public function findById(string $id): ?PlaceLead;

    /**
     * @param PlaceLead $placeLead
     * @return bool
     */

    public function insert(PlaceLead $placeLead): bool;

    /**
     * @param \backend\db\models\PlaceLead $placeLead
     * @return bool
     */
    public function update(PlaceLead $placeLead): bool;

    /**
     * @param array $placeLeads
     * @return bool
     */
    public function remove(array $placeLeads): bool;

    /**
     * @param \backend\db\models\PlaceLead $placeLead
     * @return bool
     */
    public function delete(PlaceLead $placeLead): bool;
}
