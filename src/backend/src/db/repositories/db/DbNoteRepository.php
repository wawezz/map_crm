<?php

namespace backend\db\repositories\db;

use backend\db\models\Note;
use yii\mongodb\Connection;

class DbNoteRepository extends AbstractMongoDbRepository
{
    /**
    * @var int
    */
    private $collection;

    public function __construct(Connection $db)
    {
        parent::__construct($db);

        $this->collection = $this->db->getCollection('notes');
    }

    public function findAll(array $query = array(), array $fields = array(), array $options = array()): array
    {
        
        $cursor = $this->collection->find($query, $fields, $options);
 
        return $cursor->toArray();
    }

    public function deleteAll(array $query = array(), array $options = array()): bool
    {
        
        return $this->collection->remove($query, $options);
    }

    public function findById(string $id = null): array
    {
        $query = array();

        if($id) $query['_id'] = $id;

        $result = $this->collection->findOne($query)->toArray();

        return $result;
    }
    
    public function countAllNotes($query): int
    {
        return $this->collection->count($query);
    }

    public function insert(Note $note): bool
    {
        $this->collection->insert($note->publicBundle());

        return true;
    }

    public function insertMass(array $notes): bool
    {
        $result = $this->collection->batchInsert($notes);
        return (!empty($result))?true:false;
    }
}
