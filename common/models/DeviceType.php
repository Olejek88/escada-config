<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "device_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property Device[] $devices
 *
 */
class DeviceType extends ActiveRecord
{
    const DEVICE_COUNTER = '0FBACF26-31CA-4B92-BCA3-220E09A6D2D3';
    const DEVICE_ZB_COORDINATOR = '8CF354DB-6FC2-4256-A24E-3E497BA99589';
    const DEVICE_LIGHT = 'CFD3C7CC-170C-4764-9A8D-10047C8B8B1D';
    const DEVICE_ELECTRO = '0FBACF26-31CA-4B92-BCA3-220E09A6D2D3';
    const DEVICE_ZB_COORDINATOR_E18 = 'E6AAD04B-7B05-4104-B767-E425399D8D04';

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
        return 'device_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid', 'title'], 'string', 'max' => 45],
        ];
    }

    public function fields()
    {
        return [
            '_id',
            'uuid',
            'title',
            'createdAt',
            'changedAt',
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
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    public function getDevices()
    {
        return $this->hasMany(Device::class, ['deviceTypeUuid' => 'uuid']);
    }
}
