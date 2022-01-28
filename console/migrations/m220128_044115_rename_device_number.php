<?php

use yii\db\Migration;

/**
 * Class m220128_044115_rename_device_number
 */
class m220128_044115_rename_device_number extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%device}}', 'number', 'num');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220128_044115_rename_device_number cannot be reverted.\n";
        $this->renameColumn('{{%device}}', 'num', 'number');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220128_044115_rename_device_number cannot be reverted.\n";

        return false;
    }
    */
}
