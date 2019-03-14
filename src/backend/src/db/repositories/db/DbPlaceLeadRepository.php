<?php

namespace backend\db\repositories\db;

use backend\db\models\PlaceLead;
use backend\db\normalizers\PlaceLeadNormalizer;
use backend\db\repositories\PlaceLeadRepositoryInterface;
use yii\db\Connection;

class DbPlaceLeadRepository extends AbstractDbRepository implements PlaceLeadRepositoryInterface
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
            app_place_leads.*,
            CONCAT(cu.id, "-", cu.secret) as createdBy,
            ls.name as statusName, 
            lt.name as typeName
            FROM app_place_leads
            LEFT JOIN app_users cu ON app_place_leads.createdBy = cu.id 
            LEFT JOIN app_place_types lt ON app_place_leads.type = lt.id 
            LEFT JOIN app_statuses ls ON app_place_leads.status = ls.id';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, PlaceLead::class]);
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

        $sql = 'SELECT COUNT(*) FROM app_place_leads';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_place_leads WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, PlaceLead::class]);
    }

    public function findById(string $id): ?PlaceLead
    {

        $cmd = $this->db->createCommand('SELECT app_place_leads.*, 
            cu.name as createdByName, 
            cu.secret as createdBySecret,  
            ls.name as statusName, 
            lt.name as typeName 
            FROM app_place_leads
            LEFT JOIN app_users cu ON app_place_leads.createdBy = cu.id 
            LEFT JOIN app_place_types lt ON app_place_leads.type = lt.id 
            LEFT JOIN app_statuses ls ON app_place_leads.status = ls.id 
            WHERE app_place_leads.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(PlaceLead::class) ?: null;
    }

    public function insert(PlaceLead $placeLead): bool
    {

        $cmd = $this->db->createCommand('INSERT INTO app_place_leads
                            ( id, name, address, phone, type, status, price, rating, review, website, geometry, geo, data, toSync, campaignCode,
                            isImportant, createdBy, createdAt, updatedAt, contractAt, nextFollowupDate)
                     VALUES (:id, :name, :address, :phone, :type, :status, :price, :rating, :review, :website, :geometry, :geo, :data, :toSync, :campaignCode,
                            :isImportant, :createdBy, :createdAt, :updatedAt, :contractAt, :nextFollowupDate)',
            PlaceLeadNormalizer::serialize($placeLead));

        return $cmd->execute() > 0;
    }

    public function update(PlaceLead $placeLead): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_place_leads
                   SET name = :name,
                       address = :address,
                       phone = :phone,
                       type = :type,
                       status = :status,
                       price = :price,
                       rating = :rating,
                       review = :review, 
                       website = :website, 
                       geometry = :geometry,
                       geo = :geo, 
                       data = :data, 
                       toSync = :toSync, 
                       campaignCode = :campaignCode, 
                       isImportant = :isImportant, 
                       createdBy = :createdBy, 
                       createdAt = :createdAt, 
                       updatedAt = :updatedAt, 
                       contractAt = :contractAt, 
                       nextFollowupDate = :nextFollowupDate
                 WHERE id = :id',
                 PlaceLeadNormalizer::serialize($placeLead));

        return $cmd->execute() > 0;
    }

    public function remove(array $placeLeads): bool
    {

        $cmd = $this->db->createCommand()->delete('app_place_leads', ['id' => $placeLeads]);

        return $cmd->execute() > 0;
    }

    public function delete(PlaceLead $placeLead): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_place_leads WHERE id = :id', [
            ':id' => $placeLead->id,
        ]);

        return $cmd->execute() > 0;
    }
}
