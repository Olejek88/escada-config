<?php

use common\models\MeasureType;
use yii\db\Migration;

/**
 * Class m190925_065439_add_measure_type
 */
class m190925_065439_add_measure_type extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');
        $this->insertIntoType('measure_type', MeasureType::HOP_COUNT, 'Hops', $currentTime, $currentTime);
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

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190925_065439_add_measure_type cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190925_065439_add_measure_type cannot be reverted.\n";

        return false;
    }
    */
}
