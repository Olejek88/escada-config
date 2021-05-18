<?php

use yii\db\Migration;

/**
 * Class m190627_082205_add_lastdate_update
 */
class m190627_082205_add_lastdate_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isNew = false;
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $isNew = version_compare($this->db->getServerVersion(), '5.6.1', '>');
        }

        if ($isNew) {
            $defVal = $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP');
        } else {
            $defVal = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');
        }

        $this->createTable('{{%last_update}}', [
            '_id' => $this->primaryKey(),
            'entityName' => $this->string(50)->notNull()->unique(),
            'date' => $defVal,
            'createdAt' => $defVal,
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190627_082205_add_lastdate_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190627_082205_add_lastdate_update cannot be reverted.\n";

        return false;
    }
    */
}
