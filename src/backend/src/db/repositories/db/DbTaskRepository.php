<?php

namespace backend\db\repositories\db;

use backend\db\models\Task;
use backend\db\normalizers\TaskNormalizer;
use backend\db\repositories\TaskRepositoryInterface;
use yii\db\Connection;

class DbTaskRepository extends AbstractDbRepository implements TaskRepositoryInterface
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
                    if($field == 'responsible' || $field == 'createdBy'){
                        if (mb_strlen($value) > 36 && preg_match('/-([a-f0-9]+)$/i', $value, $m)) {
                            $secret = $m[1];
                            $value = str_replace("-$secret", '', $value);
                        }
                    }
                    if($conditionValue!="NULL"){
                        if($condition == "BETWEEN"){
                            $from = $i."from";
                            $to = $i."to";
                            $where[] = "(app_tasks.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_tasks.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_tasks.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($f == 'responsible' || $f == 'createdBy'){
                            if (mb_strlen($value) > 36 && preg_match('/-([a-f0-9]+)$/i', $value, $m)) {
                                $secret = $m[1];
                                $value = str_replace("-$secret", '', $value);
                            }
                        }
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_tasks.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_tasks.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_tasks.$f $condition)";
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
            app_tasks.*, ou.name as createdByName, CONCAT(ou.id, "-", ou.secret) as createdBy, ru.name as responsibleName, CONCAT(ru.id, "-", ru.secret) as responsible
            FROM app_tasks
            LEFT JOIN app_users ou ON app_tasks.createdBy = ou.id 
            LEFT JOIN app_users ru ON app_tasks.responsible = ru.id';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Task::class]);
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
                    if($field == 'responsible' || $field == 'createdBy'){
                        if (mb_strlen($value) > 36 && preg_match('/-([a-f0-9]+)$/i', $value, $m)) {
                            $secret = $m[1];
                            $value = str_replace("-$secret", '', $value);
                        }
                    }
                    if($conditionValue!="NULL"){
                        if($condition == "BETWEEN"){
                            $from = $i."from";
                            $to = $i."to";
                            $where[] = "(app_tasks.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_tasks.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_tasks.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($f == 'responsible' || $f == 'createdBy'){
                            if (mb_strlen($value) > 36 && preg_match('/-([a-f0-9]+)$/i', $value, $m)) {
                                $secret = $m[1];
                                $value = str_replace("-$secret", '', $value);
                            }
                        }
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_tasks.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_tasks.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_tasks.$f $condition)";
                        }
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        $sql = 'SELECT COUNT(*) FROM app_tasks';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_tasks WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Task::class]);
    }

    public function findById(string $id): ?Task
    {

        $cmd = $this->db->createCommand('SELECT app_tasks.*, ou.name as createdByName, ou.secret as createdBySecret, ru.name as responsibleName, ru.secret as responsibleSecret 
            FROM app_tasks
            LEFT JOIN app_users ou ON app_tasks.createdBy = ou.id 
            LEFT JOIN app_users ru ON app_tasks.responsible = ru.id 
            WHERE app_tasks.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(Task::class) ?: null;
    }

    public function insert(Task $task): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_tasks
                            ( id,  elementId, elementType, type, responsible, createdBy, comment, eventDate, createdAt, updatedAt)
                     VALUES (:id, :elementId, :elementType, :type, :responsible, :createdBy, :comment, :eventDate, :createdAt, NOW())',
            TaskNormalizer::serialize($task));

        $cmd->execute();

        return $this->db->getLastInsertID();
    }

    public function update(Task $task): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_tasks
                   SET elementId = :elementId,
                       elementType = :elementType,
                       type = :type,
                       responsible = :responsible,
                       createdBy = :createdBy,
                       comment = :comment,
                       eventDate = :eventDate,
                       createdAt = :createdAt,
                       updatedAt = NOW()
                 WHERE id = :id',
                 TaskNormalizer::serialize($task));

        return $cmd->execute() > 0;
    }

    public function remove(array $tasks): bool
    {

        $cmd = $this->db->createCommand()->delete('app_tasks', ['id' => $tasks]);

        return $cmd->execute() > 0;
    }

    public function delete(Task $task): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_tasks WHERE id = :id', [
            ':id' => $task->id,
        ]);

        return $cmd->execute() > 0;
    }
}
