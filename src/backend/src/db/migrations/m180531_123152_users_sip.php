<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180531_123152_users_sip extends Migration
{
    public function safeUp() 
    {
        $this->addColumn('{{%users}}', 'sipId', $this->integer(11)->defaultValue(null)->after('avatarId'));
        $this->addColumn('{{%users}}', 'sipPass', $this->string(128)->defaultValue(null)->after('sipId'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'sipPass');
        $this->dropColumn('{{%users}}', 'sipId');
    }
}
