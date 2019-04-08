<?php

namespace backend\db\repositories\db;

use backend\db\models\PlaceLead;
use backend\db\models\Note;
use backend\db\normalizers\PlaceLeadNormalizer;
use backend\db\repositories\PlaceLeadRepositoryInterface;
use yii\db\Connection;

class DbPlaceLeadRepository extends AbstractDbRepository implements PlaceLeadRepositoryInterface
{

    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function findAll(int $limit = null, int $offset = null, array $filter = null, array $sort = null, string $query = null): array
    {
        $where = [];
        $params = [];
        $orderBy = [];

        if(null !== $query) {
            $params[":query"] = "%$query%";
        }

        $i = 0;
        if (null !== $filter) {
            foreach ($filter as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                $condition = " = ";
                $conditionValue = "";
                $value = explode('|', $value);

                if(count($value)==2){
                    $condition = $value[0]!="NULL"?" $value[0] ":($value[1] != 0 ? "IS NOT NULL" : "IS NULL");
                    $conditionValue = $value[0];
                }
             
                if(count($value)==3){
                    $condition = "BETWEEN";
                }else{
                    $value = end($value);
                }

                if (\is_string($field)) {
                    if($conditionValue!="NULL"){
                        if($condition == "BETWEEN"){
                            $from = $i."from";
                            $to = $i."to";
                            $where[] = "(app_place_leads.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_place_leads.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_place_leads.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_place_leads.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_place_leads.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_place_leads.$f $condition)";
                        }
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
            ST_AsText(app_place_leads.geo) as geo,
            (SELECT COUNT(*) FROM app_tasks tt WHERE tt.elementId=app_place_leads.id AND tt.elementType = '.Note::ELEMENT_TYPE_PLACE_LEAD.') as tasksCount
            FROM app_place_leads
            LEFT JOIN app_users cu ON app_place_leads.createdBy = cu.id 
            LEFT JOIN app_statuses ls ON app_place_leads.status = ls.id';

        if(null !== $query){
            $sql .= " WHERE (app_place_leads.name LIKE :query OR app_place_leads.zipCode LIKE :query OR app_place_leads.phone LIKE :query)";
        }

        if(count($where) > 0){
            $sql .= \null !== $query ? ' AND ' : ' WHERE ';
            $sql .= implode(' AND ', $where);
        }

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

    public function countAll(array $filter = null, string $query = null): int
    {
        $where = [];
        $params = [];

        if(null !== $query) {
            $params[":query"] = "%$query%";
        }

        $i = 0;
        if (null !== $filter) {
            foreach ($filter as $field => $value) {
                if (empty($value)) {
                    continue;
                }
                $condition = " = ";
                $conditionValue = "";
                $value = explode('|', $value);

                if(count($value)==2){
                    $condition = $value[0]!="NULL"?" $value[0] ":($value[1] != 0 ? "IS NOT NULL" : "IS NULL");
                    $conditionValue = $value[0];
                }

                if(count($value)==3){
                    $condition = "BETWEEN";
                }else{
                    $value = end($value);
                }

                if (\is_string($field)) {
                    if($conditionValue!="NULL"){
                        if($condition == "BETWEEN"){
                            $from = $i."from";
                            $to = $i."to";
                            $where[] = "(app_place_leads.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_place_leads.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_place_leads.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_place_leads.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_place_leads.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_place_leads.$f $condition)";
                        }
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        $sql = 'SELECT COUNT(*) FROM app_place_leads';

        if(null !== $query){
            $sql .= " WHERE (app_place_leads.name LIKE :query OR app_place_leads.zipCode LIKE :query OR app_place_leads.phone LIKE :query)";
        }

        if(count($where) > 0){
            $sql .= \null !== $query ? ' AND ' : ' WHERE ';
            $sql .= implode(' AND ', $where);
        }

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

    public function findById(int $id): ?PlaceLead
    {
        $cmd = $this->db->createCommand('SELECT app_place_leads.*, 
            cu.name as createdByName, 
            cu.secret as createdBySecret,  
            ls.name as statusName,
            ST_AsText(app_place_leads.geo) as geo
            FROM app_place_leads
            LEFT JOIN app_users cu ON app_place_leads.createdBy = cu.id 
            LEFT JOIN app_statuses ls ON app_place_leads.status = ls.id 
            WHERE app_place_leads.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(PlaceLead::class) ?: null;

    }

    public function findByPlaceId(string $placeId): ?PlaceLead
    {
        $cmd = $this->db->createCommand('SELECT app_place_leads.*, 
            cu.name as createdByName, 
            cu.secret as createdBySecret,  
            ls.name as statusName, 
            ST_AsText(app_place_leads.geo) as geo
            FROM app_place_leads
            LEFT JOIN app_users cu ON app_place_leads.createdBy = cu.id 
            LEFT JOIN app_statuses ls ON app_place_leads.status = ls.id 
            WHERE app_place_leads.placeId = :placeId', [
            ':placeId' => $placeId,
        ]);

        return $cmd->queryObject(PlaceLead::class) ?: null;

    }

    public function insert(PlaceLead $placeLead): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_place_leads
                            ( id, placeId, name, address, phone, type, status, price, rating, review, website, geo, data, toSync, campaignCode,
                            isImportant, zipCode, city, alexaRank, onlineSince, ypReviews, multiLocation, lastRemark, bbbRating, ypRating, 
                            dataScore, carrier, callerIdName, rn, createdBy, createdAt, updatedAt, contractAt, nextFollowupDate)
                     VALUES (:id, :placeId, :name, :address, :phone, :type, :status, :price, :rating, :review, :website, ST_GeomFromText(:geo), :data, :toSync, :campaignCode,
                            :isImportant, :zipCode, :city, :alexaRank, :onlineSince, :ypReviews, :multiLocation, :lastRemark, :bbbRating, :ypRating, 
                            :dataScore, :carrier, :callerIdName, :rn, :createdBy, :createdAt, NOW(), :contractAt, :nextFollowupDate)',
            PlaceLeadNormalizer::serialize($placeLead));

        $cmd->execute();

        return $this->db->getLastInsertID();
    }

    public function update(PlaceLead $placeLead): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_place_leads
                   SET placeId = :placeId,
                       name = :name,
                       address = :address,
                       phone = :phone,
                       type = :type,
                       status = :status,
                       price = :price,
                       rating = :rating,
                       review = :review, 
                       website = :website, 
                       geo = ST_GeomFromText(:geo), 
                       data = :data, 
                       toSync = :toSync, 
                       campaignCode = :campaignCode, 
                       isImportant = :isImportant, 
                       zipCode = :zipCode,
                       city = :city, 
                       alexaRank = :alexaRank, 
                       onlineSince = :onlineSince, 
                       ypReviews = :ypReviews, 
                       multiLocation = :multiLocation, 
                       lastRemark = :lastRemark, 
                       bbbRating = :bbbRating, 
                       ypRating = :ypRating, 
                       dataScore = :dataScore, 
                       carrier = :carrier, 
                       callerIdName = :callerIdName, 
                       rn = :rn,
                       createdBy = :createdBy, 
                       createdAt = :createdAt, 
                       updatedAt = NOW(), 
                       contractAt = :contractAt, 
                       nextFollowupDate = :nextFollowupDate
                 WHERE id = :id',
                 PlaceLeadNormalizer::serialize($placeLead));

        return $cmd->execute() > 0;
    }

    public function updateByID(int $id): bool
    {

        $data = array( 'id' => $id );

        $cmd = $this->db->createCommand('
                UPDATE app_place_leads
                   SET updatedAt = NOW()
                 WHERE id = :id',
                 $data);

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
