<?php

use yii\db\Migration;

/**
 * Class m190602_123639_fix_light_answer
 */
class m190602_123639_fix_light_answer extends Migration
{
    const LIGHT_ANSWER = '{{%light_answer}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn(self::LIGHT_ANSWER,'data', $this->string(512)->defaultValue(""));
        $this->alterColumn(self::LIGHT_ANSWER,'dateOut', $this->timestamp()->null()->defaultValue(null));
        $this->dropColumn(self::LIGHT_ANSWER, 'uuid');
        $this->dropIndex('address', self::LIGHT_ANSWER);
        $this->dropIndex('data', self::LIGHT_ANSWER);
        $this->dropIndex('data_2', self::LIGHT_ANSWER);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190602_123639_fix_light_answer cannot be reverted.\n";

        $this->alterColumn(self::LIGHT_ANSWER,'data', $this->string(45)->notNull()->unique());
        $this->alterColumn(self::LIGHT_ANSWER,'dateOut', $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull());

        return true;
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
