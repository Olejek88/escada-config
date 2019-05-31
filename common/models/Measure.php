<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "measure".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $sensorChannelUuid
 * @property string $measureTypeUuid
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
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
        return 'measure';
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
                    'uuid',
                    'sensorChannelUuid',
                    'measureTypeUuid',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['value'], 'number'],
            [['uuid', 'sensorChannelUuid', 'measureTypeUuid', 'date', 'oid'], 'string', 'max' => 50],
            [['oid','createdAt', 'changedAt'], 'safe'],
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
            'uuid' => Yii::t('app', 'Uuid'),
            'sensorChannel' => Yii::t('app', 'Канал измерения'),
            'sensorChannelUuid' => Yii::t('app', 'Канал измерения'),
            'measureType' => Yii::t('app', 'Тип измерения'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
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
        return ['_id', 'uuid',
            'sensorChannelUuid',
            'sensorChannel' => function ($model) {
                return $model->sensorChannel;
            },
            'measureTypeUuid',
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
        return $this->hasOne(SensorChannel::class, ['uuid' => 'sensorChannelUuid']);
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getMeasureType()
    {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    public static function getLastMeasureBetweenDates($sensorChannelUuid, $startDate, $endDate)
    {
        $model = Measure::find()->where(["sensorChannelUuid" => $sensorChannelUuid])
            ->andWhere('date >= "'.$startDate.'"')
            ->andWhere('date < "'.$endDate.'"')
            ->orderBy('date DESC')
            ->one();
        return $model;
    }

}
