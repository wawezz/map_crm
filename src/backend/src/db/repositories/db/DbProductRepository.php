<?php

namespace backend\db\repositories\db;

use backend\db\models\Product;
use backend\db\normalizers\ProductNormalizer;
use backend\db\repositories\ProductRepositoryInterface;
use yii\db\Connection;

class DbProductRepository extends AbstractDbRepository implements ProductRepositoryInterface
{

    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function findAll(int $limit = null, int $offset = null, array $filter = null, array $sort = null): array
    {
        $where = [];
        $params = [];
        $orderBy = [];

        $i = 0;
        if (null !== $filter) {
            foreach ($filter as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                $condition = " = ";
                $value = explode('|', $value);

                if(count($value)==2){
                    $condition = " $value[0] ";
                }

                $value = end($value);

                if (\is_string($field)) {
                    $where[] = "($field $condition :value$i)";
                    $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        $w[] = "($f $condition :value$i)";
                        $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        if (null !== $sort) {
            foreach ($sort as $field => $order) {
                if (empty($order)) {
                    continue;
                }

                $order = mb_strtoupper($order);

                $orderBy[] = "$field $order";
            }
        }

        $sql = 'SELECT
            app_products.* 
            FROM app_products';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Product::class]);
    }

    public function countAll(array $filter = null): int
    {
        $where = [];
        $params = [];

        $i = 0;
        if (null !== $filter) {
            foreach ($filter as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                $condition = " = ";
                $value = explode('|', $value);

                if(count($value)==2){
                    $condition = " $value[0] ";
                }

                $value = end($value);

                if (\is_string($field)) {
                    $where[] = "($field $condition :value$i)";
                    $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        $w[] = "($f $condition :value$i)";
                        $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        $sql = 'SELECT COUNT(*) FROM app_products';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_products WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Product::class]);
    }

    public function findById(string $id): ?Product
    {

        $cmd = $this->db->createCommand('SELECT app_products.* FROM app_products
            WHERE app_products.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(Product::class) ?: null;
    }

    public function insert(Product $product): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_products
                            ( id,  name)
                     VALUES (:id, :name)',
            ProductNormalizer::serialize($product));

        $cmd->execute();

        return $this->db->getLastInsertID();
    }

    public function update(Product $product): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_products
                   SET name = :name
                 WHERE id = :id',
                 ProductNormalizer::serialize($product));

        return $cmd->execute() > 0;
    }

    public function remove(array $products): bool
    {

        $cmd = $this->db->createCommand()->delete('app_products', ['id' => $products]);

        return $cmd->execute() > 0;
    }

    public function delete(Product $product): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_products WHERE id = :id', [
            ':id' => $product->id,
        ]);

        return $cmd->execute() > 0;
    }
}
