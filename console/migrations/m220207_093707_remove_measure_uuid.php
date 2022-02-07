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
        $this->dropIndex('uuid', '{{%data}}');
        $this->dropColumn('{{%data}}', 'uuid');
        // принудительно отправляем все данные на сервер, так как там мы их все удалили
        $this->update('{{%last_update}}', ['date' => date('Y-m-d H:i:s', 0)], ['entityName' => 'measure']);
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
