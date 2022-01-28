<?php

use yii\db\Migration;

/**
 * Class m220128_045528_change_device_number
 */
class m220128_045528_change_device_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%device}}', 'num', 'numOld');
        $this->addColumn('{{%device}}', 'num', $this->integer()->defaultValue(0));
        $this->dropColumn('{{%device}}', 'numOld');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220128_045528_change_device_number cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220128_045528_change_device_number cannot be reverted.\n";

        return false;
    }
    */
}
