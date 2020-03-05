<?php

use yii\db\Migration;

/**
 * Class m190824_175846_add_light_program
 */
class m190824_175846_add_light_program extends Migration
{
    const DEVICE_PROGRAM = '{{%device_program}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::DEVICE_PROGRAM, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string(45)->notNull()->defaultValue('Программа'),
            'period_title1' => $this->string(45)->notNull()->defaultValue('Вечерние сумерки')
                ->comment('Закат. Период от заката до конца вечерних сумерек.'),
            'value1' => $this->integer()->defaultValue(0),
            'period_title2' => $this->string(45)->notNull()->defaultValue('Вечер')
                ->comment('Конец вечерних сумерек. Период с конца вечерних сумерек до например полуночи.'),
            'time2' => $this->integer()->defaultValue(0),
            'value2' => $this->integer()->defaultValue(0),
            'period_title3' => $this->string(45)->notNull()->defaultValue('Ночь')
                ->comment('Ночь. Период например с полуночи до трёх часов ночи.'),
            'time3' => $this->integer()->defaultValue(0),
            'value3' => $this->integer()->defaultValue(0),
            'period_title4' => $this->string(45)->notNull()->defaultValue('Поздняя ночь')
                ->comment('Ночь. Период например с трёх часов ночи до утренних сумерек.'),
            'time4' => $this->integer()->defaultValue(0),
            'value4' => $this->integer()->defaultValue(0),
            'period_title5' => $this->string(45)->notNull()->defaultValue('Утренние сумерки')
                ->comment('Начало утренних сумерек. Период с утренних сумерек до восхода.'),
            'value5' => $this->integer()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultValue('1970-01-02 00:00:00'),
        ], $tableOptions);

        $this->createIndex('title', self::DEVICE_PROGRAM, ['title'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190824_175846_add_light_program cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190824_175846_add_light_program cannot be reverted.\n";

        return false;
    }
    */
}
