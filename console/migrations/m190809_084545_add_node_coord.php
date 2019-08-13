<?php

use yii\db\Migration;

/**
 * Class m190809_084545_add_node_coord
 */
class m190809_084545_add_node_coord extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%node}}', 'longitude', $this->double()->defaultValue(0));
        $this->addColumn('{{%node}}', 'latitude', $this->double()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190809_084545_add_node_coord cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190809_084545_add_node_coord cannot be reverted.\n";

        return false;
    }
    */
}
