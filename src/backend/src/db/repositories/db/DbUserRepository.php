<?php

namespace backend\db\repositories\db;

use backend\db\common\generator\SecretGenerator;
use backend\db\models\User;
use backend\db\normalizers\UserNormalizer;
use backend\db\repositories\UserRepositoryInterface;
use yii\db\Connection;

class DbUserRepository extends AbstractDbRepository implements UserRepositoryInterface
{
    /**
     * @var \backend\db\common\generator\SecretGenerator
     */
    private $secretGenerator;

    public function __construct(Connection $db, SecretGenerator $secretGenerator)
    {
        parent::__construct($db);

        $this->secretGenerator = $secretGenerator;
    }

    public function findAll(int $limit = null, int $offset = null, array $filter = null, array $sort = null): array
    {
        $where = [];
        $params = [];
        $orderBy = ["roleName desc"];

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
                            $where[] = "(app_users.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_users.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_users.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_users.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_users.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_users.$f $condition)";
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
            app_users.*, CONCAT(app_users.id, "-", app_users.secret) as id, app_files.name as avatarName, app_files.path as avatarPath, app_roles.name as roleName, app_groups.name as groupName
            FROM app_users
            LEFT JOIN app_files ON app_users.avatarId = app_files.id
            LEFT JOIN app_groups ON app_users.groupId = app_groups.id
            LEFT JOIN app_roles ON app_users.roleId = app_roles.id';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, User::class]);
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
                            $where[] = "(app_users.$field $condition :value$from AND :value$to)";
                            $params[":value$from"] = $value[1];
                            $params[":value$to"] = $value[2];
                        }else{
                            $where[] = "(app_users.$field $condition :value$i)";
                            $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                        }
                    }else{
                        $where[] = "(app_users.$field $condition)";
                    }
                    $i++;
                } elseif (\is_array($field)) {
                    $w = [];
                    foreach ($field as $f) {
                        if($conditionValue!="NULL"){
                            if($condition == "BETWEEN"){
                                $from = $i."from";
                                $to = $i."to";
                                $w[] = "(app_users.$f $condition :value$from AND :value$to)";
                                $params[":value$from"] = $value[1];
                                $params[":value$to"] = $value[2];
                            }else{
                                $w[] = "(app_users.$f $condition :value$i)";
                                $params[":value$i"] = $condition==" LIKE "?"%$value%":$value;
                            }
                        }else{
                            $w[] = "(app_users.$f $condition)";
                        }
                        $i++;
                    }
                    $where[] = '(' . implode(' OR ', $w) . ')';
                }
            }
        }

        $sql = 'SELECT COUNT(*) FROM app_users';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_users WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, User::class]);
    }

    public function findById(string $id): ?User
    {
        if (mb_strlen($id) > 36 && preg_match('/-([a-f0-9]+)$/i', $id, $m)) {
            $secret = $m[1];
            $id = str_replace("-$secret", '', $id);
        } else {
            $secret = null;
        }

        if (null === $secret) {
            $cmd = $this->db->createCommand('SELECT app_users.*, app_files.path as avatarPath, app_files.name as avatarName FROM app_users LEFT JOIN app_files on app_users.avatarId = app_files.id WHERE app_users.id = :id', [
                ':id' => $id,
            ]);
        } else {
            $cmd = $this->db->createCommand('SELECT app_users.*, app_files.path as avatarPath, app_files.name as avatarName FROM app_users LEFT JOIN app_files on app_users.avatarId = app_files.id WHERE app_users.id = :id AND app_users.secret = :secret', [
                ':id' => $id,
                ':secret' => $secret,
            ]);
        }

        return $cmd->queryObject(User::class) ?: null;
    }

    public function findBySipID(string $id): ?User
    {

        $cmd = $this->db->createCommand('SELECT app_users.*, app_files.path as avatarPath, app_files.name as avatarName FROM app_users LEFT JOIN app_files on app_users.avatarId = app_files.id WHERE app_users.sipId = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(User::class) ?: null;
    }

    public function findByEmail(string $email): ?User
    {
        $cmd = $this->db->createCommand('SELECT app_users.*, app_files.path as avatarPath, app_files.name as avatarName FROM app_users LEFT JOIN app_files on app_users.avatarId = app_files.id WHERE app_users.email = :email', [
            ':email' => $email,
        ]);

        return $cmd->queryObject(User::class) ?: null;
    }

    public function countByEmail(string $email): int
    {
        $cmd = $this->db->createCommand('SELECT COUNT(*) FROM app_users WHERE email = :email', [
            ':email' => $email,
        ]);

        return (int) $cmd->queryScalar();
    }

    public function insert(User $user): bool
    {

        if (empty($user->secret)) {
            $user->secret = $this->secretGenerator->generate();
        }

        if (!empty($user->password)) {
            $user->passwordHash = \Yii::$app->security->generatePasswordHash($user->password);
        }

        $cmd = $this->db->createCommand('INSERT INTO app_users
                            ( id,  email, name, roleId, groupId, avatarId, sipId, sipPass, createdAt,  updatedAt, passwordHash, secret)
                     VALUES (:id, :email, :name, :roleId, :groupId, :avatarId, :sipId, :sipPass, :createdAt, NOW(), :passwordHash, :secret)',
            UserNormalizer::serialize($user));

        return $cmd->execute() > 0;
    }

    public function update(User $user): bool
    {

        if (!empty($user->password)) {
            $user->passwordHash = \Yii::$app->security->generatePasswordHash($user->password);
        }

        $cmd = $this->db->createCommand('
                UPDATE app_users
                   SET email = :email,
                       name = :name,
                       roleId = :roleId,
                       groupId = :groupId,
                       avatarId = :avatarId,
                       sipId = :sipId, 
                       sipPass = :sipPass,
                       createdAt = :createdAt,
                       updatedAt = NOW(),
                       passwordHash = :passwordHash,
                       secret = :secret
                 WHERE id = :id',
            UserNormalizer::serialize($user));

        return $cmd->execute() > 0;
    }

    public function remove(array $users): bool
    { 

        $cmd = $this->db->createCommand()->delete('app_users', ['id' => $users]);

        return $cmd->execute() > 0;
    }

    public function delete(User $user): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_users WHERE id = :id', [
            ':id' => $user->id,
        ]);

        return $cmd->execute() > 0;
    }
}
