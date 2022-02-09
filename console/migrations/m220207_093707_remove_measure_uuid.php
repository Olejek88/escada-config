<?php

use yii\db\Migration;

/**
 * Class m220207_093707_remove_measure_uuid
 */
class m220207_093707_remove_measure_uuid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // удаляем все данные, т.к. в базе нет правильных _id в таблице sensor_channel с сервера
        $this->truncateTable('{{%data}}');

        // удаляем т.к. бесполезные данные
        $this->dropIndex('uuid', '{{%data}}');
        $this->dropColumn('{{%data}}', 'uuid');

        // удаляем старые индексы
        $this->dropForeignKey('fk-measure-sensorChannelUuid', '{{%data}}');
        $this->dropIndex('idx-sensorChannelUuid', '{{%data}}');
        $this->dropIndex('data-scuuid-date-idx', '{{%data}}');
        $this->dropIndex('data-scuuid-t-p-d-idx', '{{%data}}');
        // удаляем столбец, который заменим на тип int
        $this->dropColumn('{{%data}}', 'sensorChannelUuid');
        // добавляем столбец для ссылки на канал измерений, который будет лучше индексироваться
        $this->addColumn('{{%data}}', 'sensorChannelId', $this->integer()->notNull());
        // добавляем необходимые индексы
        $this->createIndex('idx-data-sensorChannelId-date', '{{%data}}', [
            'sensorChannelId',
            'date'
        ]);
        $this->createIndex('idx-data-sensorChannelId-type-parameter-date', '{{%data}}', [
            'sensorChannelId', 'type', 'parameter', 'date'
        ]);
        $this->addForeignKey(
            'fk-data-sensorChannelId',
            '{{%data}}',
            'sensorChannelId',
            '{{%sensor_channel}}',
            '_id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        // удаляем все каналы измерений для того чтоб затянуть их по новой с правильными _id
        $this->db->createCommand("SET FOREIGN_KEY_CHECKS=0;")->execute();
        $this->truncateTable('{{%sensor_channel}}');
        $this->db->createCommand("SET FOREIGN_KEY_CHECKS=1;")->execute();

        // принудительно затягиваем все каналы измерений
        $this->update('{{%last_update}}', ['date' => date('Y-m-d H:i:s', 1)], ['entityName' => 'sensor_channel_download']);
        // для порядка сбрасываем дату отправки последних измерений
        $this->update('{{%last_update}}', ['date' => date('Y-m-d H:i:s', 1)], ['entityName' => 'measure']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220207_093707_remove_measure_uuid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220207_093707_remove_measure_uuid cannot be reverted.\n";

        return false;
    }
    */
}
