<?php

use yii\db\Migration;

/**
 * Class m220113_075235_add_lost_light
 */
class m220113_075235_add_lost_light extends Migration
{
    const LOST_LIGHT = '{{%lost_light}}';

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

        $this->createTable(self::LOST_LIGHT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'date' => $defVal,
            'title' => $this->string(150)->notNull(),
            'status' => $this->string(64)->notNull(),
            'macAddress' => $this->string(150)->notNull(),
            'deviceUuid' => $this->string(36)->notNull(),
            'nodeUuid' => $this->string(36)->notNull(),
            'createdAt' => $defVal,
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220113_075235_add_lost_light cannot be reverted.\n";
        $this->dropTable(self::LOST_LIGHT);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220113_075235_add_lost_light cannot be reverted.\n";

        return false;
    }
    */
}
