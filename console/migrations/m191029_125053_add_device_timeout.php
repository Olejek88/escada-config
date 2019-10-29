<?php

use yii\db\Migration;

/**
 * Class m191029_125053_add
 */
class m191029_125053_add_device_timeout extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // добавляем поле таймаута для устройства, после которого считаем что с устройством нет связи
        // по умолчанию таймаут задан в 600 секунд (10 минут)
        $this->addColumn('{{device}}', 'linkTimeout', $this->integer()->notNull()->defaultValue(600));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191029_125053_add cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191029_125053_add cannot be reverted.\n";

        return false;
    }
    */
}
