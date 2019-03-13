<?php

namespace backend\db\repositories\db;

use backend\db\models\Group;
use yii\db\Connection;

class DbGroupRepository extends AbstractDbRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function findAll(): array
    {
        $where = [];
        $params = [];
        $orderBy = [];

        $i = 0;

        $sql = 'SELECT * FROM app_groups';

        $sql .= \count($where) > 0 ? (' WHERE ' . implode(' AND ', $where)) : '';
        $sql .= \count($orderBy) > 0 ? (' ORDER BY ' . implode(', ', $orderBy)) : '';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll([\PDO::FETCH_CLASS, Group::class]);
    }
}
