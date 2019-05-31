<?php

namespace common\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "journal".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $userUuid
 * @property string $description
 * @property string $date
 *
 * @property User $user
 */
class Journal extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'journal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userUuid', 'description'], 'required'],
            [['description'], 'string'],
            [['date'], 'safe'],
            [['userUuid'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => '№',
            'userUuid' => 'Uuid Пользователя',
            'description' => 'Описание',
            'date' => 'Дата',
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(
            User::class, ['uuid' => 'userUuid']
        );
    }
}
