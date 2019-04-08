<?php

namespace backend\db\repositories\db;

use backend\db\models\Lead;
use backend\db\normalizers\LeadNormalizer;
use backend\db\repositories\LeadRepositoryInterface;
use yii\db\Connection;

class DbLeadRepository extends AbstractDbRepository implements LeadRepositoryInterface
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
                            $where[] = "(app_leads.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_leads.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_leads.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_leads.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_leads.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_leads.$f $condition)";
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
            app_leads.*, ac.name as clientName, 
            ru.name as responsibleName, 
            CONCAT(ru.id, "-", ru.secret) as responsible,
            cu.name as createdByName, 
            CONCAT(cu.id, "-", cu.secret) as createdBy,
            ls.name as statusName, 
            lc.name as countryName
            FROM app_leads
            LEFT JOIN app_clients ac ON app_leads.client = ac.id
            LEFT JOIN app_users ru ON app_leads.responsible = ru.id 
            LEFT JOIN app_users cu ON app_leads.createdBy = cu.id 
            LEFT JOIN app_countries lc ON app_leads.countryId = lc.id 
            LEFT JOIN app_statuses ls ON app_leads.status = ls.id';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Lead::class]);
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
                            $where[] = "(app_leads.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_leads.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_leads.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_leads.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_leads.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_leads.$f $condition)";
                        }
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        $sql = 'SELECT COUNT(*) FROM app_leads';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_leads WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Lead::class]);
    }

    public function findById(int $id): ?Lead
    {

        $cmd = $this->db->createCommand('SELECT app_leads.*, ac.name as clientName, 
            ru.name as responsibleName, 
            ru.secret as responsibleSecret,
            cu.name as createdByName, 
            cu.secret as createdBySecret,  
            ls.name as statusName, 
            lc.name as countryName 
            FROM app_leads
            LEFT JOIN app_clients ac ON app_leads.client = ac.id
            LEFT JOIN app_users ru ON app_leads.responsible = ru.id 
            LEFT JOIN app_users cu ON app_leads.createdBy = cu.id 
            LEFT JOIN app_countries lc ON app_leads.countryId = lc.id 
            LEFT JOIN app_statuses ls ON app_leads.status = ls.id 
            WHERE app_leads.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(Lead::class) ?: null;
    }

    public function insert(Lead $lead): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_leads
                            ( id, name, client, responsible, createdBy, status, createdAt,  updatedAt, completedAt, budget, orderId,
                            firstCallAt, countryId, currency, product, productCount, productPrice, shippingPrice, postOrder, rejectionReason)
                     VALUES (:id, :name, :client, :responsible, :createdBy, :status, :createdAt, NOW(), :completedAt, :budget, :orderId,
                            :firstCallAt, :countryId, :currency, :product, :productCount, :productPrice, :shippingPrice, :postOrder, :rejectionReason)',
            LeadNormalizer::serialize($lead));

        $cmd->execute();

        return $this->db->getLastInsertID();
    }

    public function update(Lead $lead): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_leads
                   SET name = :name,
                       client = :client,
                       responsible = :responsible,
                       createdBy = :createdBy,
                       status = :status,
                       createdAt = :createdAt,
                       updatedAt = NOW(),
                       completedAt = :completedAt, 
                       budget = :budget, 
                       orderId = :orderId,
                       firstCallAt = :firstCallAt, 
                       countryId = :countryId, 
                       currency = :currency, 
                       product = :product, 
                       productCount = :productCount, 
                       productPrice = :productPrice, 
                       shippingPrice = :shippingPrice, 
                       postOrder = :postOrder, 
                       rejectionReason = :rejectionReason
                 WHERE id = :id',
                 LeadNormalizer::serialize($lead));

        return $cmd->execute() > 0;
    }

    public function updateByID(int $id): bool
    {

        $data = array( 'id' => $id );

        $cmd = $this->db->createCommand('
                UPDATE app_leads
                   SET updatedAt = NOW()
                 WHERE id = :id',
                 $data);

        return $cmd->execute() > 0;
    }

    public function remove(array $leads): bool
    {

        $cmd = $this->db->createCommand()->delete('app_leads', ['id' => $leads]);

        return $cmd->execute() > 0;
    }

    public function delete(Lead $lead): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_leads WHERE id = :id', [
            ':id' => $lead->id,
        ]);

        return $cmd->execute() > 0;
    }
}
