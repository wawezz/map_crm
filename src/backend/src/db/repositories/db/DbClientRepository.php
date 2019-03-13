<?php

namespace backend\db\repositories\db;

use backend\db\models\Client;
use backend\db\normalizers\ClientNormalizer;
use backend\db\repositories\ClientRepositoryInterface;
use yii\db\Connection;

class DbClientRepository extends AbstractDbRepository implements ClientRepositoryInterface
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
            app_clients.*, CONCAT(ou.id, "-", ou.secret) as createdBy, ou.name as creatorName, CONCAT(ru.id, "-", ru.secret) as responsible, ru.name as responsibleName, app_countries.name as countryName
            FROM app_clients
            LEFT JOIN app_users ou ON app_clients.createdBy = ou.id
            LEFT JOIN app_users ru ON app_clients.responsible = ru.id
            LEFT JOIN app_countries ON app_clients.countryId = app_countries.id';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        if (null !== $offset) {
            $sql .= " OFFSET $offset";
        }

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Client::class]);
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

        $sql = 'SELECT COUNT(*) FROM app_clients';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryScalar();
    }

    public function findLike(string $field, string $query, int $limit = null): array
    {
        $sql = "SELECT * FROM app_clients WHERE $field LIKE :query ORDER BY createdAt DESC";

        if (null !== $limit) {
            $sql .= " LIMIT $limit";
        }

        $cmd = $this->db->createCommand($sql, [
            ':query' => $query,
        ]);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Client::class]);
    }

    public function findById(string $id): ?Client
    {

        $cmd = $this->db->createCommand('SELECT app_clients.*, ou.secret as createdBySecret, ru.secret as responsibleSecret FROM app_clients
            LEFT JOIN app_users ou on app_clients.createdBy = ou.id
            LEFT JOIN app_users ru ON app_clients.responsible = ru.id
            WHERE app_clients.id = :id', [
            ':id' => $id,
        ]);

        return $cmd->queryObject(Client::class) ?: null;
    }

    public function findByPhone(string $phone): ?Client
    {

        $cmd = $this->db->createCommand('SELECT app_clients.*, ou.secret as createdBySecret, ru.secret as responsibleSecret FROM app_clients
            LEFT JOIN app_users ou on app_clients.createdBy = ou.id
            LEFT JOIN app_users ru ON app_clients.responsible = ru.id
            WHERE app_clients.phone = :phone', [
            ':phone' => $phone,
        ]);

        return $cmd->queryObject(Client::class) ?: null;
    }

    public function findPhoneLike(string $phone): ?Client
    {

        $cmd = $this->db->createCommand('SELECT app_clients.*, ou.secret as createdBySecret, ru.secret as responsibleSecret FROM app_clients
            LEFT JOIN app_users ou on app_clients.createdBy = ou.id
            LEFT JOIN app_users ru ON app_clients.responsible = ru.id
            WHERE app_clients.phone LIKE :phone OR app_clients.workPhone LIKE :phone OR app_clients.otherPhone LIKE :phone', [
            ':phone' => "%$phone%",
        ]);

        return $cmd->queryObject(Client::class) ?: null;
    }

    public function insert(Client $client): int
    {
        $cmd = $this->db->createCommand('INSERT INTO app_clients
                            ( id,  email, emailVerified, name, surname, phone, phoneVerified, workPhone, otherPhone, countryId, state, city, street, building, flat, createdBy, responsible, createdAt,  updatedAt, zip, skype)
                     VALUES (:id, :email, :emailVerified, :name, :surname, :phone, :phoneVerified, :workPhone, :otherPhone, :countryId, :state, :city, :street, :building, :flat, :createdBy, :responsible, :createdAt, :updatedAt, :zip, :skype)',
            ClientNormalizer::serialize($client));

        $cmd->execute();

        return $this->db->getLastInsertID();
    }

    public function update(Client $client): bool
    {

        $cmd = $this->db->createCommand('
                UPDATE app_clients
                   SET email = :email,
                       emailVerified = :emailVerified,
                       name = :name,
                       surname = :surname,
                       phone = :phone,
                       phoneVerified = :phoneVerified,
                       workPhone = :workPhone,
                       otherPhone = :otherPhone,
                       countryId = :countryId,
                       state = :state,
                       city = :city,
                       street = :street,
                       building = :building,
                       flat = :flat,
                       createdBy = :createdBy,
                       responsible = :responsible,
                       createdAt = :createdAt,
                       updatedAt = :updatedAt,
                       zip = :zip,
                       skype = :skype
                 WHERE id = :id',
            ClientNormalizer::serialize($client));

        return $cmd->execute() > 0;
    }

    public function remove(array $clients): bool
    {

        $cmd = $this->db->createCommand()->delete('app_clients', ['id' => $clients]);

        return $cmd->execute() > 0;
    }

    public function delete(Client $client): bool
    {
        $cmd = $this->db->createCommand('DELETE FROM app_clients WHERE id = :id', [
            ':id' => $client->id,
        ]);

        return $cmd->execute() > 0;
    }
}
