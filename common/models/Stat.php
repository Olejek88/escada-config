<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "stat"
 *
 * @property integer $_id
 * @property string $uuid
 * @property integer $type
 * @property double $cpu
 * @property double $mem
 * @property string $createdAt
 * @property string $changedAt
 *
 */

class Stat extends ActiveRecord
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
        return 'stat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid'], 'required'],
            [['createdAt', 'changedAt', 'type'], 'safe'],
            [['uuid', 'cpu', 'mem'], 'double'],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'cpu',
            'mem',
            'type',
            'createdAt',
            'changedAt'
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
            'cpu' => Yii::t('app', 'CPU'),
            'mem' => Yii::t('app', 'Память'),
            'type' => Yii::t('app', 'Тип'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен')
        ];
    }
}
