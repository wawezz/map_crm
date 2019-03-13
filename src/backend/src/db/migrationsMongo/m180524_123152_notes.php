<?php

namespace backend\db\migrationsMongo;

use yii\mongodb\Migration;

class m180524_123152_notes extends Migration
{
    private $collection = 'notes';
 
    public function up()
    {
        $this->createCollection($this->collection);
        $this->createIndex($this->collection, 'elementId');
        $this->createIndex($this->collection, 'elementType');
        $this->createIndex($this->collection, 'noteType');
        $this->createIndex($this->collection, 'createdBy');
    }
 
    public function down()
    {
        $this->dropIndex($this->collection, 'createdBy');
        $this->dropIndex($this->collection, 'noteType');
        $this->dropIndex($this->collection, 'elementType');
        $this->dropIndex($this->collection, 'elementId');
        $this->dropCollection($this->collection);
    }
}
