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
 * @property ActiveQuery $deviceStatus
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
