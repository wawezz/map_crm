<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180524_123152_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%leads}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->defaultValue(null),
            'client' => $this->integer(11),
            'responsible' => $this->string(36)->defaultValue(null),
            'status' => $this->integer(11),
            'createdBy' => $this->string(36),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'completedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%leads}}');
    }
}
