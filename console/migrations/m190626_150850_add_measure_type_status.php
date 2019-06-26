<?php

use yii\db\Migration;
use common\models\MeasureType;

/**
 * Class m190626_150850_add_measure_type_status
 */
class m190626_150850_add_measure_type_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoType('measure_type', MeasureType::STATUS, 'Статус', $currentTime, $currentTime);
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt)
    {
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $createdAt,
            'changedAt' => $changedAt
        ]);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190626_150850_add_measure_type_status cannot be reverted.\n";

        return false;
    }
    */

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190626_150850_add_measure_type_status cannot be reverted.\n";

        return false;
    }

}
