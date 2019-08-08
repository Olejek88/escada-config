<?php

use yii\db\Migration;

/**
 * Class m190808_094110_add_new_fields
 */
class m190808_094110_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%node}}', 'lastDate', $this->timestamp()->defaultValue('2019-01-01'));
        $this->addColumn('{{%node}}', 'security', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%node}}', 'phone', $this->string()->defaultValue(""));
        $this->addColumn('{{%node}}', 'software', $this->string()->defaultValue("2.0.1"));
        $this->addColumn('{{%data}}', 'parameter', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190808_094110_add_new_fields cannot be reverted.\n";

        return false;
    }
}
