<?php
namespace common\models;

use common\components\MtmActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "sensor_config".
 *
 * @property integer $_id
 * @property string $oid идентификатор организации
 * @property string $uuid
 * @property string $deviceUuid
 * @property string $parameter
 * @property string $value
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Device $device
 */
class DeviceConfig extends MtmActiveRecord
{
    const PARAM_SET_VALUE = 'Уровень освещения';
    const PARAM_FREQUENCY = 'Частота выдачи датчиком статуса';
    const PARAM_REGIME = 'Режим работы светильника';
    const PARAM_POWER = 'Мощность светильника';
    const PARAM_GROUP = 'Группа';

    const PARAM_TIME0 = 'Время с начала суток #1';
    const PARAM_LEVEL0 = 'Уровень освещения #1';
    const PARAM_TIME1 = 'Время с начала суток #2';
    const PARAM_LEVEL1 = 'Уровень освещения #2';
    const PARAM_TIME2 = 'Время с начала суток #3';
    const PARAM_LEVEL2 = 'Уровень освещения #3';
    const PARAM_TIME3 = 'Время с начала суток #4';
    const PARAM_LEVEL3 = 'Уровень освещения #4';

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
        return 'device_config';
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
                    'deviceUuid',
                    'parameter',
                    'value'
                ],
                'required'
            ],
            [['oid','createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'deviceUuid'
                ],
                'string', 'max' => 45
            ],
            [['oid'], 'checkOrganizationOwn'],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields1()
    {
        return ['_id', 'uuid', 'parameter', 'value',
            'deviceUuid',
            'device' => function ($model) {
                return $model->device;
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
            'device' => Yii::t('app', 'Устройство'),
            'deviceUuid' => Yii::t('app', 'Устройство'),
            'parameter' => Yii::t('app', 'Параметр'),
            'value' => Yii::t('app', 'Конфигурация'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getDevice()
    {
        return $this->hasOne(
            Device::class, ['uuid' => 'deviceUuid']
        );
    }

}
