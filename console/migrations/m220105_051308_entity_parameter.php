<?php

use yii\db\Migration;

/**
 * Class m220105_051308_entity_parameter
 */
class m220105_051308_entity_parameter extends Migration
{
    const ENTITY_PARAMETER = '{{%entity_parameter}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        $isNew = false;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $isNew = version_compare($this->db->getServerVersion(), '5.6.1', '>');
        }

        if ($isNew) {
            $defVal = $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP');
        } else {
            $defVal = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');
        }

        $this->createTable(self::ENTITY_PARAMETER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'entityUuid' => $this->string(36)->notNull(),
            'parameter' => $this->string()->notNull(),
            'value' => $this->text()->null(),
            'createdAt' => $defVal,
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-entityParameter-entityUuid',
            self::ENTITY_PARAMETER,
            ['entityUuid']
        );

        $this->createIndex(
            'idx-entityParameter-entityUuid-parameter',
            self::ENTITY_PARAMETER,
            ['entityUuid', 'parameter'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220105_051308_ext_device_info cannot be reverted.\n";
        $this->dropTable(self::ENTITY_PARAMETER);
        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220105_051308_ext_device_info cannot be reverted.\n";

        return false;
    }
    */
}
