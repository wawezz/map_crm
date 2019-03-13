<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180530_123152_tasks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tasks}}', [
            'id' => $this->primaryKey(11),
            'elementId' => $this->integer(11),
            'elementType' => $this->integer(11),
            'type' => $this->string(128),
            'responsible' => $this->string(36)->defaultValue(null),
            'createdBy' => $this->string(36),
            'comment' => $this->string(255),
            'eventDate' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tasks}}');
    }
}
