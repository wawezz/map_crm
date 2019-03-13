<?php

namespace backend\db\migrations;

use yii\db\Migration;

class m180526_123152_leads_fields extends Migration
{
    public function safeUp() 
    {
        $this->addColumn('{{%leads}}', 'budget', $this->smallInteger(5)->after('status'));
        $this->addColumn('{{%leads}}', 'orderId', $this->string(11)->after('id'));
        $this->addColumn('{{%leads}}', 'firstCallAt', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->after('createdAt'));
        $this->addColumn('{{%leads}}', 'countryId', $this->integer(11)->after('budget'));
        $this->addColumn('{{%leads}}', 'currency', $this->integer(11)->after('budget'));
        $this->addColumn('{{%leads}}', 'product', $this->integer(11)->after('currency'));
        $this->addColumn('{{%leads}}', 'productCount', $this->smallInteger(5)->defaultValue(0)->after('product'));
        $this->addColumn('{{%leads}}', 'productPrice', $this->integer(11)->after('productCount'));
        $this->addColumn('{{%leads}}', 'crossProduct', $this->integer(11)->after('productPrice'));
        $this->addColumn('{{%leads}}', 'crossProductCount', $this->smallInteger(5)->defaultValue(0)->after('crossProduct'));
        $this->addColumn('{{%leads}}', 'crossProductPrice', $this->integer(11)->after('crossProductCount'));
        $this->addColumn('{{%leads}}', 'upsellProduct', $this->integer(11)->after('crossProductPrice'));
        $this->addColumn('{{%leads}}', 'upsellProductCount', $this->smallInteger(5)->defaultValue(0)->after('upsellProduct'));
        $this->addColumn('{{%leads}}', 'upsellProductPrice', $this->integer(11)->after('upsellProductCount'));
        $this->addColumn('{{%leads}}', 'shippingPrice', $this->integer(11)->after('upsellProductPrice'));
        $this->addColumn('{{%leads}}', 'postOrder', $this->boolean()->defaultValue(0)->after('shippingPrice'));
        $this->addColumn('{{%leads}}', 'rejectionReason', $this->string(255)->defaultValue(null)->after('postOrder'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'budget');
        $this->dropColumn('{{%leads}}', 'orderId');
        $this->dropColumn('{{%leads}}', 'firstCallAt');
        $this->dropColumn('{{%leads}}', 'countryId');
        $this->dropColumn('{{%leads}}', 'currency');
        $this->dropColumn('{{%leads}}', 'product');
        $this->dropColumn('{{%leads}}', 'productCount');
        $this->dropColumn('{{%leads}}', 'productPrice');
        $this->dropColumn('{{%leads}}', 'crossProduct');
        $this->dropColumn('{{%leads}}', 'crossProductCount');
        $this->dropColumn('{{%leads}}', 'crossProductPrice');
        $this->dropColumn('{{%leads}}', 'upsellProduct');
        $this->dropColumn('{{%leads}}', 'upsellProductCount');
        $this->dropColumn('{{%leads}}', 'upsellProductPrice');
        $this->dropColumn('{{%leads}}', 'shippingPrice');
        $this->dropColumn('{{%leads}}', 'postOrder');
        $this->dropColumn('{{%leads}}', 'rejectionReason');
    }
}
