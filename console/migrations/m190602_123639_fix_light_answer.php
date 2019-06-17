<?php

use yii\db\Migration;

/**
 * Class m190602_123639_fix_light_answer
 */
class m190602_123639_fix_light_answer extends Migration
{
    const LIGHT_ANSWER = '{{%light_answer}}';
    const LIGHT_MESSAGE = '{{%light_message}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->alterColumn(self::LIGHT_ANSWER, 'data', $this->string(1024)->defaultValue(""));
        $this->alterColumn(self::LIGHT_ANSWER,'dateOut', $this->timestamp()->null()->defaultValue(null));
        $this->dropColumn(self::LIGHT_ANSWER, 'uuid');
        $this->dropIndex('address', self::LIGHT_ANSWER);
        $this->dropIndex('data', self::LIGHT_ANSWER);

        $this->createTable(self::LIGHT_MESSAGE, [
            '_id' => $this->primaryKey(),
            'address' => $this->string(45)->notNull(),
            'data' => $this->string(1024)->notNull(),
            'dateIn' => $this->timestamp()->defaultValue('0000-00-00 00:00:00')->notNull(),
            'dateOut' => $this->timestamp()->null()->defaultValue(null),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultValue('0000-00-00 00:00:00'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190602_123639_fix_light_answer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190602_123639_fix_light_answer cannot be reverted.\n";

        return false;
    }
    */
}
