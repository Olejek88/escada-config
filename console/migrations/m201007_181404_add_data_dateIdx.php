<?php

use yii\db\Migration;

/**
 * Class m201007_181404_add_data_dateIdx
 */
class m201007_181404_add_data_dateIdx extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('data-date-idx', 'data', ['date']);
        $this->createIndex('data-scuuid-date-idx', 'data', ['sensorChannelUuid', 'date']);
        $this->createIndex('data-scuuid-t-p-d-idx', 'data', ['sensorChannelUuid', 'type', 'parameter', 'date']);

        $this->createIndex('dev_reg-duuid-date-idx', 'device_register', ['deviceUuid', 'date']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201007_181404_add_data_dateIdx cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201007_181404_add_data_dateIdx cannot be reverted.\n";

        return false;
    }
    */
}
