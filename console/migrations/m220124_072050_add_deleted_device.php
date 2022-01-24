<?php

use yii\db\Migration;

/**
 * Class m220124_072050_add_deleted_device
 */
class m220124_072050_add_deleted_device extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%device}}', 'deleted', $this->tinyInteger()->notNull()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220124_072050_add_deleted_device cannot be reverted.\n";
        $this->dropColumn('{{device}}', 'deleted');
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_072050_add_deleted_device cannot be reverted.\n";

        return false;
    }
    */
}
