<?php

use console\yii2\Migration;

/**
 * Class m190412_104112_init_new
 */
class m190412_104112_init_new extends Migration
{
    /**
     * {@inheritdoc}
     */
    const CAMERA = '{{%camera}}';
    const DEVICE = '{{%device}}';
    const DEVICE_REGISTER = '{{%device_register}}';
    const DEVICE_STATUS = '{{%device_status}}';
    const DEVICE_TYPE = '{{%device_type}}';
    const JOURNAL = '{{%journal}}';
    const MESSAGE = '{{%message}}';
    const MEASURE = '{{%data}}';
    const MEASURE_TYPE = '{{%measure_type}}';
    const NODE = '{{%node}}';
    const SENSOR_CHANNEL = '{{%sensor_channel}}';
    const SENSOR_CONFIG = '{{%sensor_config}}';
    const USER = '{{%user}}';

    const CONFIG = '{{%config}}';
    const INFO = '{{%info}}';
    const INTERFACES = '{{%interfaces}}';
    const LIGHT_ANSWER = '{{%light_answer}}';
    const LIGHT_MESSAGE = '{{%light_message}}';
    const PROTOCOLS = '{{%protocols}}';
    const REGISTER = '{{%register}}';
    const STAT = '{{%stat}}';
    const THREADS = '{{%threads}}';

    const FK_RESTRICT = 'RESTRICT';
    const FK_CASCADE = 'CASCADE';

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::USER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),

            'type' => $this->integer()->notNull(),
            'name' => $this->string()->notNull(),
            'whoIs' => $this->string(45)->defaultValue(""),
            'image' => $this->string(),
            'contact' => $this->string()->notNull(),
            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::DEVICE_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::DEVICE_STATUS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(
            self::NODE,
            [
                '_id' => $this->primaryKey()->comment("id"),
                'uuid' => $this->string(45)->unique()->notNull(),
                'address' => $this->string(45),
                'deviceStatusUuid' => $this->string(45)->notNull(),
                'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ], $tableOptions
        );

        $this->createIndex(
            'idx-deviceStatusUuid',
            self::NODE,
            'deviceStatusUuid'
        );

        $this->addForeignKey(
            'fk-node-deviceStatusUuid',
            self::NODE,
            'deviceStatusUuid',
            'device_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable(
            self::CAMERA,
            [
                '_id' => $this->primaryKey()->comment("Id"),
                'uuid' => $this->string(45)->unique()->notNull(),
                'title' => $this->string(150)->notNull(),
                'deviceStatusUuid' => $this->string(45)->notNull(),
                'nodeUuid' => $this->string(45)->notNull(),
                'address' => $this->string(45)->notNull(),
                'port' => $this->integer()->notNull(),
                'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
                'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            ], $tableOptions
        );

        $this->createIndex(
            'idx-deviceStatusUuid',
            self::CAMERA,
            'deviceStatusUuid'
        );

        $this->addForeignKey(
            'fk-camera-deviceStatusUuid',
            self::CAMERA,
            'deviceStatusUuid',
            'device_status',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-camera-nodeUuid',
            self::CAMERA,
            'nodeUuid'
        );

        $this->addForeignKey(
            'fk-camera-nodeUuid',
            self::CAMERA,
            'nodeUuid',
            'node',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable(self::DEVICE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'thread' => $this->integer()->notNull()->defaultValue(1),
            'address' => $this->integer()->notNull(),
            'object' => $this->string(50)->notNull()->defaultValue("none"),
            'last_date' => $this->timestamp()->defaultValue('2019-01-01'),
            'q_att' => $this->integer()->notNull()->defaultValue(0),
            'q_errors' => $this->integer()->notNull()->defaultValue(0),
            'dev_time' => $this->timestamp()->defaultValue('2019-01-01'),
            'name' => $this->string(150)->notNull(),
            'port' => $this->string(50)->notNull(),
            'protocol' => $this->integer()->notNull()->defaultValue(1),
            'number' => $this->string(150)->notNull(),
            'nodeUuid' => $this->string(45)->notNull(),
            'deviceTypeUuid' => $this->string(45)->notNull(),
            'deviceStatusUuid' => $this->string(45)->notNull(),
            'serial' => $this->string(),
            'interface' => $this->smallInteger()->defaultValue(1),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-nodeUuid',
            self::DEVICE,
            'nodeUuid'
        );

        $this->addForeignKey(
            'fk-device-nodeUuid',
            self::DEVICE,
            'nodeUuid',
            self::NODE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-deviceTypeUuid',
            self::DEVICE,
            'deviceTypeUuid'
        );

        $this->addForeignKey(
            'fk-device-deviceTypeUuid',
            self::DEVICE,
            'deviceTypeUuid',
            self::DEVICE_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-deviceStatusUuid',
            self::DEVICE,
            'deviceStatusUuid'
        );

        $this->addForeignKey(
            'fk-device-deviceStatusUuid',
            self::DEVICE,
            'deviceStatusUuid',
            self::DEVICE_STATUS,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::DEVICE_REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'deviceUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->defaultValue('2019-01-01'),
            'description' => $this->string(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-deviceUuid',
            self::DEVICE_REGISTER,
            'deviceUuid'
        );

        $this->addForeignKey(
            'fk-device_register-deviceUuid',
            self::DEVICE_REGISTER,
            'deviceUuid',
            self::DEVICE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable('{{%journal}}', [
            '_id' => $this->primaryKey(),
            'userUuid' => $this->string(45)->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'description' => $this->string()->defaultValue("")
        ]);

        $this->createIndex(
            'idx-userUuid',
            'journal',
            'userUuid'
        );

        $this->addForeignKey(
            'fk-journal-userUuid',
            'journal',
            'userUuid',
            'user',
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        //--------------------------------------------------------------------------------------------------------------
        $this->createTable('{{%measure_type}}', [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable(self::MEASURE, ['_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'measureTypeUuid' => $this->string(45)->notNull(),
            'sensorChannelUuid' => $this->string(45)->notNull(),
            'value' => $this->double(),
            'type' => $this->integer()->defaultValue(0),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-sensorChannelUuid',
            self::MEASURE,
            'sensorChannelUuid'
        );

        $this->createTable(self::SENSOR_CHANNEL, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'register' => $this->string()->notNull(),
            'deviceUuid' => $this->string(45),
            'measureTypeUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-deviceUuid',
            self::SENSOR_CHANNEL,
            'deviceUuid'
        );

        $this->addForeignKey(
            'fk-sensor_channel-deviceUuid',
            self::SENSOR_CHANNEL,
            'deviceUuid',
            self::DEVICE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addForeignKey(
            'fk-measure-sensorChannelUuid',
            self::MEASURE,
            'sensorChannelUuid',
            self::SENSOR_CHANNEL,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-measureTypeUuid',
            self::MEASURE,
            'measureTypeUuid'
        );

        $this->addForeignKey(
            'fk-measure-measureTypeUuid',
            self::MEASURE,
            'measureTypeUuid',
            self::MEASURE_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );
        //--------------------------------------------------------------------------------------------------------------

        $this->createTable(self::MESSAGE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'link' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);


        $this->createTable(self::SENSOR_CONFIG, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'config' => $this->string(),
            'sensorChannelUuid' => $this->string(45)->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-sensorChannelUuid',
            self::SENSOR_CONFIG,
            'sensorChannelUuid'
        );

        $this->addForeignKey(
            'fk-shutdown-sensorChannelUuid',
            self::SENSOR_CONFIG,
            'sensorChannelUuid',
            self::SENSOR_CHANNEL,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::CONFIG, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'address' => $this->string()->notNull(),
            'log' => $this->integer()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::INFO, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'log' => $this->string(45)->notNull()->unique(),
            'time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'linux' => $this->string(45)->notNull()->unique(),
            'hardware' => $this->string(45)->notNull()->unique(),
            'base_name' => $this->string(45)->notNull()->unique(),
            'software' => $this->string(45)->notNull()->unique(),
            'ip' => $this->string(45)->notNull()->unique(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::INTERFACES, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::LIGHT_ANSWER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'address' => $this->string(45)->notNull()->unique(),
            'data' => $this->string(45)->notNull()->unique(),
            'dateIn' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'dateOut' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::PROTOCOLS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'type' => $this->integer()->notNull(),
            'protocol' => $this->integer()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::STAT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'type' => $this->integer()->notNull(),
            'cpu' => $this->double()->notNull(),
            'mem' => $this->double()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::THREADS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'deviceUuid' => $this->string(45),
            'port' => $this->string(50)->notNull(),
            'speed' => $this->integer()->notNull()->defaultValue(19200),
            'title' => $this->string(150)->notNull(),
            'status' => $this->integer()->notNull()->defaultValue(0),
            'work' => $this->integer()->notNull()->defaultValue(0),
            'deviceTypeUuid' => $this->string(45),
            'c_time' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'message' => $this->string(250)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-deviceUuid',
            self::THREADS,
            'deviceUuid'
        );

        $this->addForeignKey(
            'fk-threads-deviceUuid',
            self::THREADS,
            'deviceUuid',
            self::DEVICE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-deviceTypeUuid',
            self::THREADS,
            'deviceTypeUuid'
        );

        $this->addForeignKey(
            'fk-threads-deviceTypeUuid',
            self::THREADS,
            'deviceTypeUuid',
            self::DEVICE_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );


    }

    /**
     * {@inheritdoc}
     */
    public
    function safeDown()
    {

        return true;
    }
}
