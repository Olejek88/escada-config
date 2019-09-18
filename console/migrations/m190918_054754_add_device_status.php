<?php

use common\models\DeviceStatus;
use yii\db\Migration;

/**
 * Class m190918_054754_add_device_status
 */
class m190918_054754_add_device_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoStatus('{{%device_status}}', DeviceStatus::NOT_LINK, 'Нет связи', $currentTime, $currentTime);
    }

    private function insertIntoStatus($table, $uuid, $title, $createdAt, $changedAt)
    {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190918_054754_add_device_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190918_054754_add_device_status cannot be reverted.\n";

        return false;
    }
    */
}
