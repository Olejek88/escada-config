<?php

use yii\db\Migration;

/**
 * Class m190626_085924_add_sound_file
 */
class m190626_085924_add_sound_file extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $isNew = false;
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            $isNew = version_compare($this->db->getServerVersion(), '5.6.1', '>');
        }

        if ($isNew) {
            $defVal = $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP');
        } else {
            $defVal = $this->timestamp()->notNull()->defaultValue('0000-00-00 00:00:00');
        }

        $soundFile = '{{%sound_file}}';
        $this->createTable($soundFile, [
            '_id' => $this->primaryKey()->comment("Id"),
            'uuid' => $this->string(45)->unique()->notNull(),
            'title' => $this->string(150)->notNull(),
            'soundFile' => $this->string(512)->notNull(),
            'nodeUuid' => $this->string(45)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $defVal,
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-sound_files-nodeUuid',
            $soundFile,
            'nodeUuid'
        );

        $this->addForeignKey(
            'fk-sound_files-nodeUuid',
            $soundFile,
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
        echo "m190626_085924_add_sound_file cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190626_085924_add_sound_file cannot be reverted.\n";

        return false;
    }
    */
}
