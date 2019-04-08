<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180534_123152_statuses_fields extends Migration
{
    public function safeUp() 
    {
        $this->addColumn('{{%statuses}}', 'followUp', $this->boolean()->defaultValue(0)->after('name'));
        $this->addColumn('{{%statuses}}', 'isMove', $this->boolean()->defaultValue(0)->after('followUp'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%statuses}}', 'followUp');
        $this->dropColumn('{{%statuses}}', 'isMove');
    }
}
