<?php

namespace backend\db\repositories\db;

use backend\db\models\Note;
use yii\db\Connection;

class DbParamsRepository extends AbstractDbRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    public function findElementsByName($name): array
    {

        $where = [];
        $params = [];
        $orderBy = [];
        
        $i = 0;

        $sql = 'SELECT id, name, '.Note::ELEMENT_TYPE_CLIENT.' as source FROM app_clients WHERE name LIKE "%'.$name.'%" 
            UNION ALL
            SELECT id, name, '.Note::ELEMENT_TYPE_PLACE_LEAD.' as source FROM app_place_leads WHERE name LIKE "%'.$name.'%" 
            UNION ALL
            SELECT id, name, '.Note::ELEMENT_TYPE_LEAD.' as source FROM app_leads WHERE name LIKE "%'.$name.'%" ';

        $cmd = $this->db->createCommand($sql, $params);

        return $cmd->queryAll();
    }
}
