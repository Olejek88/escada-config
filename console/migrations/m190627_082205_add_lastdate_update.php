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
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%last_update}}', [
            '_id' => $this->primaryKey(),
            'entityName' => $this->string(50)->notNull()->unique(),
            'date' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
            'createdAt' => $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00'),
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
