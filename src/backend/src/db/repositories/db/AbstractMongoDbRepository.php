<?php

namespace backend\db\repositories\db;
use Yii;
use yii\mongodb\Connection;

class AbstractMongoDbRepository
{
    /**
     * @var \yii\mongodb\Connection
     */
    protected $db;

    /**
     * AbstractMongoDbRepository constructor.
     * @param \yii\mongodb\Connection $db
     */
    public function __construct(Connection $db)
    {
        $this->setDb($db);
    }

    public function setDb(Connection $db): void
    {
        $this->db = Yii::$app->get('mongodb');
    }
}
