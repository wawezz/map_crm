<?php

namespace backend\db\migrations;

use yii\db\Migration;
use yii\db\Query;

class m180327_113043_users_secret extends Migration
{
    public function safeUp()
    {
        $this->addColumn(
            '{{%users}}',
            'secret',
            $this->char(12)->notNull()
        );

        $users = (new Query)->select('*')
            ->from('{{%users}}')
            ->all($this->db);

        foreach ($users as $user) {
            $this->update(
                '{{%users}}',
                ['secret' => $this->generateRandomHex()],
                ['id' => $user['id']]
            );
        }
    }

    public function safeDown()
    {
        $this->dropColumn('{{%users}}', 'secret');
    }

    private function generateRandomHex($length = 12): string
    {
        $source = '0123456789abcdef';

        $random = str_shuffle(str_repeat($source, 8));

        return mb_substr($random, 0, $length);
    }
}
