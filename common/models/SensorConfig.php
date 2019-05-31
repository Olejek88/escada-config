<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sensor_config".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $sensorChannelUuid
 * @property string $config
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property SensorChannel $sensorChannel
 */
class SensorConfig extends ActiveRecord
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
        return 'sensor_config';
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
                    'config',
                ],
                'required'
            ],
            [['oid','createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'sensorChannelUuid'
                ],
                'string', 'max' => 45
            ],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'config',
            'sensorChannelUuid',
            'sensorChannel' => function ($model) {
                return $model->contragent;
            },
            'createdAt', 'changedAt'
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
            'sensorChannelUuid' => Yii::t('app', 'Канал/устройство'),
            'config' => Yii::t('app', 'Конфигурация'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getSensorChannel()
    {
        return $this->hasOne(
            SensorChannel::class, ['uuid' => 'sensorChannelUuid']
        );
    }

}
