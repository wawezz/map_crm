<?php

namespace backend\db\repositories;

use backend\db\models\Product;

interface ProductRepositoryInterface
{
    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filter
     * @param array|null $sort
     * @return \backend\db\models\Product[]
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
     * @return \backend\db\models\Product[]
     */
    public function findLike(string $field, string $query, int $limit = null): array;

    /**
     * @param string $id
     * @return \backend\db\models\Product|null
     */
    public function findById(string $id): ?Product;

    /**
     * @param Product $product
     * @return int
     */

    public function insert(Product $product): int;

    /**
     * @param \backend\db\models\Product $product
     * @return bool
     */
    public function update(Product $product): bool;

    /**
     * @param array $products
     * @return bool
     */
    public function remove(array $products): bool;

    /**
     * @param \backend\db\models\Product $product
     * @return bool
     */
    public function delete(Product $product): bool;
}
