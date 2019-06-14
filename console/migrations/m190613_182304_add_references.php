<?php

use yii\db\Migration;

/**
 * Class m190613_182304_add_references
 */
class m190613_182304_add_references extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->insertIntoType('measure_type','29A52371-E9EC-4D1F-8BCB-80F489A96DD3',
            'Напряжение электроэнергии', $currentTime, $currentTime);
        $this->insertIntoType('measure_type','041DED21-D211-4C0B-BCD6-02E392654332',
            'Частота', $currentTime, $currentTime);
        $this->insertIntoType('measure_type','E38C561F-9E88-407E-A465-83803A625627',
            'Ток', $currentTime, $currentTime);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190613_182304_add_references cannot be reverted.\n";

        return false;
    }

    private function insertIntoType($table, $uuid, $title, $createdAt, $changedAt) {
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
        echo "m190613_182304_add_references cannot be reverted.\n";

        return false;
    }
    */
}
