<?php

use yii\db\Migration;

/**
 * Class m220123_111722_add_available_device
 */
class m220123_111722_add_available_device extends Migration
{
    const AVAILABLE_DEVICE = '{{%available_device}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        $isNew = false;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $isNew = version_compare($this->db->getServerVersion(), '5.6.1', '>');
        }

        if ($isNew) {
            $defVal = $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP');
        } else {
            $defVal = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');
        }

        $this->createTable(self::AVAILABLE_DEVICE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'macAddress' => $this->string(32)->notNull()->unique(),
            'parentMacAddress' => $this->string(32)->notNull(),
            'shortAddress' => $this->string(32)->notNull(),
            'createdAt' => $defVal,
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220123_111722_add_available_device cannot be reverted.\n";
        $this->dropTable(self::AVAILABLE_DEVICE);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220123_111722_add_available_device cannot be reverted.\n";

        return false;
    }
    */
}
