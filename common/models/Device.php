<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "device".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $address
 * @property string $name
 * @property string $deviceTypeUuid
 * @property string $serial
 * @property string $port
 * @property integer $interface
 * @property string $deviceStatusUuid
 * @property string $last_date
 * @property string $nodeUuid
 * @property string $object
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property int $thread [int(11)]
 * @property int $q_att [int(11)]
 * @property int $q_errors [int(11)]
 * @property int $dev_time [timestamp]
 * @property int $protocol [int(11)]
 * @property string $number [varchar(150)]
 *
 * @property DeviceStatus $deviceStatus
 * @property DeviceType $deviceType
 */
class Device extends ActiveRecord
{

    /**
     * Behaviors.
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
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'device';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'address','name',
            'nodeUuid',
            'node' => function ($model) {
                return $model->node;
            },
            'deviceTypeUuid',
            'deviceType' => function ($model) {
                return $model->deviceType;
            },
            'deviceStatusUuid',
            'deviceStatus' => function ($model) {
                return $model->deviceStatus;
            },
            'serial', 'last_date', 'port', 'interface',
            'createdAt', 'changedAt'
        ];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'nodeUuid',
                    'deviceTypeUuid',
                    'deviceStatusUuid',
                    'serial',
                    'interface',
                    'port'
                ],
                'required'
            ],
            [['last_date', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'deviceTypeUuid',
                    'deviceStatusUuid',
                    'serial',
                    'name',
                    'port',
                    'nodeUuid',
                    'address'
                ],
                'string', 'max' => 50
            ],
            [['interface'], 'integer'],
        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'interface' => Yii::t('app', 'Интерфейс'),
            'deviceTypeUuid' => Yii::t('app', 'Тип оборудования'),
            'deviceType' => Yii::t('app', 'Тип'),
            'last_date' => Yii::t('app', 'Дата последней связи'),
            'deviceStatusUuid' => Yii::t('app', 'Статус'),
            'deviceStatus' => Yii::t('app', 'Статус'),
            'nodeUuid' => Yii::t('app', 'Шкаф установки'),
            'node' => Yii::t('app', 'Шкаф установки'),
            'name' => Yii::t('app', 'Название'),
            'port' => Yii::t('app', 'Порт'),
            'serial' => Yii::t('app', 'Серийный номер'),
            'address' => Yii::t('app', 'Адрес'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
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
    public function getDeviceStatus()
    {
        return $this->hasOne(
            DeviceStatus::class, ['uuid' => 'deviceStatusUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getDeviceType()
    {
        return $this->hasOne(
            DeviceType::class, ['uuid' => 'deviceTypeUuid']
        );
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getNode()
    {
        return $this->hasOne(Node::class, ['uuid' => 'nodeUuid']);
    }

}
