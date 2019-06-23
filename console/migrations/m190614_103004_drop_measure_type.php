<?php

use yii\db\Migration;

/**
 * Class m190614_103004_drop_measure_type
 */
class m190614_103004_drop_measure_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    const MEASURE = '{{%data}}';
    const MEASURE_TYPE = '{{%measure_type}}';

    public function safeUp()
    {
        $this->dropForeignKey('fk-measure-measureTypeUuid', self::MEASURE);
        $this->dropIndex('idx-measureTypeUuid', self::MEASURE);
        $this->dropColumn(self::MEASURE, 'measureTypeUuid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190614_103004_drop_measure_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190614_103004_drop_measure_type cannot be reverted.\n";

        return false;
    }
    */
}
