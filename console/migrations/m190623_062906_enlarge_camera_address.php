<?php

use yii\db\Migration;

/**
 * Class m190623_062906_enlarge_camera_address
 */
class m190623_062906_enlarge_camera_address extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%camera}}', 'address', $this->string(1024)->notNull());
        $this->dropColumn('{{%camera}}', 'port');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190623_062906_enlarge_camera_address cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190623_062906_enlarge_camera_address cannot be reverted.\n";

        return false;
    }
    */
}
