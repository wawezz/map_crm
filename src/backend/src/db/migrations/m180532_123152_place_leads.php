<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180532_123152_place_leads extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%place_leads}}', [
            'id' => 'char(36) not null',
            'name' => $this->string(128)->defaultValue(null),
            'address' => $this->string(255)->defaultValue(null),
            'phone' => $this->string(32),
            'type' => $this->string(128),
            'status' => $this->integer(11),
            'price' => $this->integer(11)->defaultValue(0),
            'rating' => $this->double()->defaultValue(0),
            'review' => $this->string(128),
            'website' => $this->string(128)->defaultValue(null),
            'geo' => 'GEOMETRY',
            'data' => $this->json(),
            'toSync' => $this->boolean()->defaultValue(0),
            'campaignCode' => $this->string(128),
            'isImportant' => $this->boolean()->defaultValue(0),
            'createdBy' => $this->string(36),
            'createdAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updatedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'contractAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'nextFollowupDate' => $this->timestamp()->defaultValue(null)
        ], 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

        $this->addPrimaryKey('app_place_leads_pkey', '{{%place_leads}}', ['id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%place_leadss}}');
    }
}
