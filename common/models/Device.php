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
 * @property string $oid идентификатор организации
 * @property string $address
 * @property string $deviceTypeUuid
 * @property string $serial
 * @property string $port
 * @property integer $interface
 * @property string $deviceStatusUuid
 * @property string $date
 * @property string $nodeUuid
 * @property string $objectUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property boolean $deleted
 *
 * @property Objects $object
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
        return ['_id', 'uuid', 'address',
            'nodeUuid',
            'node' => function ($model) {
                return $model->node;
            },
            'objectUuid',
            'object' => function ($model) {
                return $model->object;
            },
            'deviceTypeUuid',
            'deviceType' => function ($model) {
                return $model->deviceType;
            },
            'deviceStatusUuid',
            'deviceStatus' => function ($model) {
                return $model->deviceStatus;
            },
            'serial', 'date', 'port', 'deleted', 'interface',
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
                    'objectUuid',
                    'serial',
                    'interface',
                    'port'
                ],
                'required'
            ],
            [['date', 'oid', 'createdAt', 'changedAt'], 'safe'],
            [['deleted'], 'boolean'],
            [
                [
                    'uuid',
                    'deviceTypeUuid',
                    'deviceStatusUuid',
                    'objectUuid',
                    'serial',
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
            'date' => Yii::t('app', 'Дата последней связи'),
            'deviceStatusUuid' => Yii::t('app', 'Статус'),
            'deviceStatus' => Yii::t('app', 'Статус'),
            'objectUuid' => Yii::t('app', 'Объект'),
            'object' => Yii::t('app', 'Объект'),
            'nodeUuid' => Yii::t('app', 'Шкаф установки'),
            'node' => Yii::t('app', 'Шкаф установки'),
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

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getObject()
    {
        return $this->hasOne(Objects::class, ['uuid' => 'objectUuid']);
    }
    /**
     * Объект связанного поля.
     *
     * @return string
     */
    public function getFullTitle()
    {
        return $this['object']->getFullTitle().' ['.$this['deviceType']['title'].']';
    }
}
