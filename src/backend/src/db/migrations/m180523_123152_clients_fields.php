<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180523_123152_clients_fields extends Migration
{
    public function safeUp() 
    {
        $this->addColumn('{{%clients}}', 'surname', $this->string(128)->after('name'));
        $this->addColumn('{{%clients}}', 'emailVerified', $this->boolean()->defaultValue(0)->after('email'));
        $this->addColumn('{{%clients}}', 'phoneVerified', $this->boolean()->defaultValue(0)->after('phone'));
        $this->addColumn('{{%clients}}', 'otherPhone', $this->string(32)->defaultValue(null)->after('workPhone'));
        $this->addColumn('{{%clients}}', 'zip', 'char(11) null');
        $this->addColumn('{{%clients}}', 'flat', $this->string(32)->defaultValue(null)->after('countryId'));
        $this->addColumn('{{%clients}}', 'building', $this->string(32)->defaultValue(null)->after('countryId'));
        $this->addColumn('{{%clients}}', 'street', $this->string(32)->defaultValue(null)->after('countryId'));
        $this->addColumn('{{%clients}}', 'city', $this->string(32)->defaultValue(null)->after('countryId'));
        $this->addColumn('{{%clients}}', 'state', $this->string(32)->defaultValue(null)->after('countryId'));
        $this->addColumn('{{%clients}}', 'skype', $this->string(32)->defaultValue(null)->after('zip'));

    }

    public function safeDown()
    {
        $this->dropColumn('{{%clients}}', 'surname');
        $this->dropColumn('{{%clients}}', 'emailVerified');
        $this->dropColumn('{{%clients}}', 'phoneVerified');
        $this->dropColumn('{{%clients}}', 'otherPhone');
        $this->dropColumn('{{%clients}}', 'zip');
        $this->dropColumn('{{%clients}}', 'flat');
        $this->dropColumn('{{%clients}}', 'building');
        $this->dropColumn('{{%clients}}', 'street');
        $this->dropColumn('{{%clients}}', 'city');
        $this->dropColumn('{{%clients}}', 'state');
        $this->dropColumn('{{%clients}}', 'skype');
    }
}
