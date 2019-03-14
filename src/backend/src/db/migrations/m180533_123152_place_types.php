<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180533_123152_place_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%place_types}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(128)->defaultValue(null)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%place_types}}');
    }
}
