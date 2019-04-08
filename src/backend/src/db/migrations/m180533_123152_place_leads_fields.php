<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180533_123152_place_leads_fields extends Migration
{
    public function safeUp() 
    {
        $this->addColumn('{{%place_leads}}', 'zipCode', $this->string(11)->defaultValue(0)->after('campaignCode'));
        $this->addColumn('{{%place_leads}}', 'city', $this->string(32)->defaultValue(null)->after('zipCode'));
        $this->addColumn('{{%place_leads}}', 'alexaRank', $this->integer(11)->defaultValue(0)->after('city'));
        $this->addColumn('{{%place_leads}}', 'onlineSince', $this->string(25)->defaultValue(null)->after('alexaRank'));
        $this->addColumn('{{%place_leads}}', 'ypReviews', $this->integer(11)->defaultValue(0)->after('onlineSince'));
        $this->addColumn('{{%place_leads}}', 'multiLocation', $this->integer(11)->defaultValue(0)->after('ypReviews'));
        $this->addColumn('{{%place_leads}}', 'lastRemark', $this->text()->defaultValue(null)->after('multiLocation'));
        $this->addColumn('{{%place_leads}}', 'bbbRating', $this->integer(4)->defaultValue(0)->after('lastRemark'));
        $this->addColumn('{{%place_leads}}', 'ypRating', $this->integer(11)->defaultValue(0)->after('bbbRating'));
        $this->addColumn('{{%place_leads}}', 'dataScore', $this->integer(11)->defaultValue(0)->after('ypRating'));
        $this->addColumn('{{%place_leads}}', 'carrier', $this->string(64)->defaultValue(null)->after('dataScore'));
        $this->addColumn('{{%place_leads}}', 'callerIdName', $this->string(64)->defaultValue(null)->after('carrier'));
        $this->addColumn('{{%place_leads}}', 'rn', $this->integer(4)->defaultValue(0)->after('callerIdName'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%place_leads}}', 'zipCode');
        $this->dropColumn('{{%place_leads}}', 'city');
        $this->dropColumn('{{%place_leads}}', 'alexaRank');
        $this->dropColumn('{{%place_leads}}', 'onlineSince');
        $this->dropColumn('{{%place_leads}}', 'ypReviews');
        $this->dropColumn('{{%place_leads}}', 'multiLocation');
        $this->dropColumn('{{%place_leads}}', 'lastRemark');
        $this->dropColumn('{{%place_leads}}', 'bbbRating');
        $this->dropColumn('{{%place_leads}}', 'ypRating');
        $this->dropColumn('{{%place_leads}}', 'dataScore');
        $this->dropColumn('{{%place_leads}}', 'carrier');
        $this->dropColumn('{{%place_leads}}', 'callerIdName');
        $this->dropColumn('{{%place_leads}}', 'rn');
    }
}
