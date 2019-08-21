<?php

use yii\db\Migration;

/**
 * Class m190816_122507_add_device_config
 */
class m190816_122507_add_device_config extends Migration
{
    const DEVICE_CONFIG = '{{%device_config}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::DEVICE_CONFIG, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(45)->notNull()->unique(),
            'deviceUuid' => $this->string(45)->notNull(),
            'parameter' => $this->string(),
            'value' => $this->string(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-deviceUuid',
            self::DEVICE_CONFIG,
            'deviceUuid'
        );

        $this->addForeignKey(
            'fk-device_config-deviceUuid',
            self::DEVICE_CONFIG,
            'deviceUuid',
            'device',
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
        echo "m190816_122507_add_device_config cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190816_122507_add_device_config cannot be reverted.\n";

        return false;
    }
    */
}
