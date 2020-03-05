<?php

use yii\db\Migration;

/**
 * Class m191020_090916_add_calendar
 */
class m191020_090916_add_calendar extends Migration
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


        // удаляем настройки программ для всех устройств, так как программы теперь будут связанны с группами
        $this->delete('{{%device_config}}', ['parameter' => 'Программа']);

        $this->createTable('{{%group}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string(100)->notNull(),
            'groupId' => $this->integer()->notNull(),
            'deviceProgramUuid' => $this->string(45)->null(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultValue('1970-01-02 00:00:00'),
        ], $tableOptions);

        // добавляем связь с программой управления
        $this->addForeignKey(
            'fk-group_deviceProgramUuid-program_uuid',
            '{{%group}}',
            'deviceProgramUuid',
            '{{%device_program}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%group_control}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'groupUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->defaultValue('1970-01-02 00:00:00')->notNull(),
            'type' => $this->integer()->notNull(),
            'deviceProgramUuid' => $this->string(45)->null(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultValue('1970-01-02 00:00:00'),
        ], $tableOptions);

        // добавляем связь с программой управления
        $this->addForeignKey(
            'fk-group_control_deviceProgramUuid-program_uuid',
            '{{%group_control}}',
            'deviceProgramUuid',
            '{{%device_program}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable('{{%node_control}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'nodeUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->notNull()->defaultValue('1970-01-02 00:00:00'),
            'type' => $this->integer()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultValue('1970-01-02 00:00:00'),
        ], $tableOptions);

        // создаём уникальный ключ для связки nodeUuid, date, type для того чтобы
        // для одного шкафа была только одна запись с восходом/закатом на указанное время
        $this->createIndex('idx-node_control-nodeUuid-date-type', '{{%node_control}}',
            ['nodeUuid', 'date', 'type'], true);

        // связь со шкафом
        $this->addForeignKey(
            'fk-node_control-nodeUuid-node-uuid',
            '{{%node_control}}',
            'nodeUuid',
            '{{%node}}',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191020_090916_add_calendar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191020_090916_add_calendar cannot be reverted.\n";

        return false;
    }
    */
}
