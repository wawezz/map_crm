<?php

namespace backend\db\common\generator;

use backend\db\common\IdGeneratorInterface;
use yii\db\Connection;

class UuidGenerator implements IdGeneratorInterface
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function generate(): string
    {
        return $this->db->createCommand('SELECT UUID()')->queryScalar();
    }
}
