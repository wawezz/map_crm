<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180429_123152_files extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%files}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->defaultValue(null),
            'path' => $this->string(128)->defaultValue(null),
            'type' => $this->string(128)->defaultValue(null),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%files}}');
    }
}
