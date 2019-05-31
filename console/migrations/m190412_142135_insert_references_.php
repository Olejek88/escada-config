<?php

use common\models\User;
use common\models\Users;
use console\yii2\Migration;

/**
 * Class m190412_142135_insert_references_
 */

class m190412_142135_insert_references_ extends Migration
{
    /**
     * {@inheritdoc}
     */
    const AUTH_KEY = 'K4g2d-bTENTHzzAJp22G1yF6otaUj9EF';

    public function safeUp()
    {
        $currentTime = date('Y-m-d\TH:i:s');

        $this->insert('{{%user}}', [
            '_id' => '1',
            'uuid' => '041DED21-D211-4C0B-BCD6-02E392654332',
            'username' => 'dev',
            'auth_key' => 'f1elprxfre3ri79clcY2VcaBdPqhPLZQ',
            'password_hash' => '$2y$13$nGZaF9DU5t/v63X./MM3Gu/eg0HsXBRtnBZ7adA3spSbJUKtLIEbC',
            'email' => 'shtrmvk@gmail.com',
            'status' => '10',
            'type' => 1,
            'name' => 'Сервис',
            'whoIs' => 'Специалист',
            'contact' => '+79227000293',
            'created_at' => $currentTime,
            'updated_at' => $currentTime
        ]);

        $this->insertIntoType('device_status','E681926C-F4A3-44BD-9F96-F0493712798D',
            'В порядке', $currentTime, $currentTime);
        $this->insertIntoType('device_status','D5D31037-6640-4A8B-8385-355FC71DEBD7',
            'Неисправно', $currentTime, $currentTime);
        $this->insertIntoType('device_status','A01B7550-4211-4D7A-9935-80A2FC257E92',
            'Отсутствует', $currentTime, $currentTime);

        $this->insertIntoType('device_type','0FBACF26-31CA-4B92-BCA3-220E09A6D2D3',
            'Электросчетчик', $currentTime, $currentTime);
        $this->insertIntoType('device_type','CFD3C7CC-170C-4764-9A8D-10047C8B8B1D',
            'Умный светильник', $currentTime, $currentTime);

        $this->insertIntoType('measure_type','7BDB38C7-EF93-49D4-8FE3-89F2A2AEDB48',
            'Мощность электроэнергии', $currentTime, $currentTime);
        $this->insertIntoType('measure_type','54051538-38F7-44A3-A9B5-C8B5CD4A2936',
            'Температура', $currentTime, $currentTime);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190412_142135_insert_references_ cannot be reverted.\n";

        return true;
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
        echo "m190412_142135_insert_references_ cannot be reverted.\n";

        return false;
    }
    */
}
