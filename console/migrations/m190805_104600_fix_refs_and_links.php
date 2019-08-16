<?php

use yii\db\Migration;
use common\models\DeviceType;

/**
 * Class m190805_104600_fix_refs_and_links
 */
class m190805_104600_fix_refs_and_links extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $date = date('Y-m-d H:i:s');
        $this->insert('{{%device_type}}', [
            'uuid' => DeviceType::DEVICE_ZB_COORDINATOR,
            'title' => 'ZigBee координатор',
            'createdAt' => $date,
            'changedAt' => $date,
        ]);

        $this->dropColumn('{{%device}}', 'thread');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190805_104600_fix_refs_and_links cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190805_104600_fix_refs_and_links cannot be reverted.\n";

        return false;
    }
    */
}
