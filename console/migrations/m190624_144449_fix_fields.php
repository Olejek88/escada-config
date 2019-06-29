<?php

use yii\db\Migration;

/**
 * Class m190624_144449_fix_fields
 */
class m190624_144449_fix_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%device}}', 'address', $this->string(150)->notNull());
        $this->createIndex('idx-device-address', '{{%device}}', ['address']);
        $this->createIndex('idx-device-register', '{{%sensor_channel}}', ['register']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190624_144449_fix_fields cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190624_144449_fix_fields cannot be reverted.\n";

        return false;
    }
    */
}
