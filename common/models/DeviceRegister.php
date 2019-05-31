<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "device_register".
 *
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $deviceUuid
 * @property string $date
 * @property string $description
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Device $device
 */

class DeviceRegister extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'device_register';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'deviceUuid', 'date'], 'required'],
            [['data'], 'safe'],
            [['uuid', 'deviceUuid', 'oid'], 'string', 'max' => 50],
            [['description'], 'string', 'max' => 250],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uuid' => Yii::t('app', 'Uuid'),
            'deviceUuid' => Yii::t('app', 'Оборудование'),
            'device' => Yii::t('app', 'Оборудование'),
            'description' => Yii::t('app', 'Запись'),
            'date' => Yii::t('app', 'Дата'),
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

    public function fields()
    {
        return ['uuid',
            'device' => function ($model) {
                return $model->device;
            }, 'date', 'description', 'createdAt', 'changedAt'
        ];
    }
}