<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "data".
 *
 * @property integer $_id
 * @property string $sensorChannelId
 * @property double $value
 * @property int $type
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 * @property int $parameter
 *
 * @property SensorChannel $sensorChannel
 * @property MeasureType $measureType
 */
class Measure extends ActiveRecord
{

    /**
     * Behaviors
     *
     * @return array
     */
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'data';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'sensorChannelId',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['sensorChannelId', 'value'], 'number'],
            [['date'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'sensorChannel' => Yii::t('app', 'Канал измерения'),
            'sensorChannelId' => Yii::t('app', 'Канал измерения'),
            'measureType' => Yii::t('app', 'Тип измерения'),
            'value' => Yii::t('app', 'Значение'),
            'date' => Yii::t('app', 'Дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id',
            'sensorChannelId',
            'sensorChannel' => function ($model) {
                return $model->sensorChannel;
            },
            'measureType' => function ($model) {
                return $model->measureType;
            },
            'value',
            'date',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getSensorChannel()
    {
        return $this->hasOne(SensorChannel::class, ['_id' => 'sensorChannelId']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->sensorChannel->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    public static function getLastMeasureBetweenDates($sensorChannelId, $startDate, $endDate)
    {
        $model = Measure::find()->where(["sensorChannelId" => $sensorChannelId])
            ->andWhere('date >= "' . $startDate . '"')
            ->andWhere('date < "' . $endDate . '"')
            ->orderBy('date DESC')
            ->limit(1)
            ->one();
        return $model;
    }

}
