<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180522_123152_countries extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%countries}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->unique(),
            'currencies' => $this->json()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%countries}}');
    }
}
