<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180501_123152_clients extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%clients}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->defaultValue(null),
            'email' => $this->string(128)->defaultValue(null),
            'phone' => $this->string(32)->unique(),
            'workPhone' => $this->string(32),
            'countryId' => $this->integer(11),
            'createdBy' => $this->string(36),
            'responsible' => $this->string(36)->defaultValue(null),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%clients}}');
    }
}
