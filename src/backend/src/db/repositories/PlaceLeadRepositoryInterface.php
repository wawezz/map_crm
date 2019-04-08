<?php

namespace backend\db\repositories;

use backend\db\models\PlaceLead;

interface PlaceLeadRepositoryInterface
{
    /**
     * @param string|null $query
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\PlaceLead[]
     */
    public function findAll(int $limit = null, int $offset = null, array $filter = null, array $sort = null, string $query = null): array;

    /**
     * @param string|null $query
     * @param array|null $filter
     * @return int
     */
    public function countAll(array $filter = null, string $query = null): int;

    /**
     * @param string $field
     * @param string $query
     * @param int|null $limit
     * @return \backend\db\models\PlaceLead[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param int $id
     * @return \backend\db\models\PlaceLead|null
     */
    public function findById(int $id): ?PlaceLead;

    /**
     * @param string $placeId
     * @return \backend\db\models\PlaceLead|null
     */
    public function findByPlaceId(string $placeId): ?PlaceLead;

    /**
     * @param PlaceLead $placeLead
     * @return int
     */

    public function insert(PlaceLead $placeLead): int;

    /**
     * @param \backend\db\models\PlaceLead $placeLead
     * @return bool
     */
    public function update(PlaceLead $placeLead): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function updateByID(int $id): bool;

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
