<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "protocol".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $name
 * @property integer $protocol
 * @property integer $type
 * @property string $createdAt
 * @property string $changedAt
 */

class Protocols extends ActiveRecord
{
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'protocol';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'name', 'protocol', 'type'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'name'], 'string', 'max' => 50],
            [['protocol', 'type'], 'integer'],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'name',
            'protocol',
            'type',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'name' => Yii::t('app', 'Название'),
            'protocol' => Yii::t('app', 'Протокол'),
            'type' => Yii::t('app', 'Тип'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
