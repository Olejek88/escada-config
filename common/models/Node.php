<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "node".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $address
 * @property string $deviceStatusUuid
 * @property string $createdAt
 * @property string $changedAt
 * @property int $oid
 * @property double $longitude
 * @property double $latitude
 *
 * @property DeviceStatus $deviceStatus
 * @property int $lastDate
 * @property bool $security
 * @property string $phone
 * @property string $software
 */
class Node extends ActiveRecord
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
        return 'node';
    }

    /**
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'oid',
            'deviceStatusUuid',
            'deviceStatus' => function ($model) {
                return $model->deviceStatus;
            },
            'address', 'createdAt', 'changedAt'
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
                    'deviceStatusUuid',
                    'oid',
                ],
                'required'
            ],
            [['address', 'createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'deviceStatusUuid',
                    'address'
                ],
                'string', 'max' => 50
            ],
            [
                ['longitude', 'latitude'],
                'double'
            ],
            [
                ['oid'], 'integer'
            ],
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
            'deviceStatusUuid' => Yii::t('app', 'Статус'),
            'deviceStatus' => Yii::t('app', 'Статус'),
            'address' => Yii::t('app', 'Адрес'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
            'longitude' => Yii::t('app', 'Долгота'),
            'latitude' => Yii::t('app', 'Широта'),
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

}
