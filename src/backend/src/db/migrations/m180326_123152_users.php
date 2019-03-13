<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180326_123152_users extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => 'char(36) not null',
            'email' => $this->string(128)->unique(),
            'name' => $this->string(128)->defaultValue(null),
            'roleId' => $this->integer(11),
            'groupId' => $this->integer(11),
            'avatarId' => $this->integer(11),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'passwordHash' => $this->string(128)
        ]);

        $this->addPrimaryKey('app_users_pkey', '{{%users}}', ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%users}}');
    }
}
