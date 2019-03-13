<?php

namespace backend\db\repositories\db;

use yii\db\Connection;

class AbstractDbRepository
{
    /**
     * @var \yii\db\Connection
     */
    protected $db;

    /**
     * AbstractDbRepository constructor.
     * @param \yii\db\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->setDb($db);
    }

    public function setDb(Connection $db): void
    {
        $this->db = $db;
    }
}
