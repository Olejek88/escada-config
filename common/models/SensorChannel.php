<?php

namespace common\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sensor_channel".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $oid идентификатор организации
 * @property string $title
 * @property string $register
 * @property string $deviceUuid
 * @property string $measureTypeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class SensorChannel extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sensor_channel';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'deviceUuid', 'measureTypeUuid'], 'required'],
            [['oid','createdAt', 'changedAt'], 'safe'],
            [['uuid', 'deviceUuid', 'register', 'measureTypeUuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
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
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'device' => Yii::t('app', 'Устройство'),
            'deviceUuid' => Yii::t('app', 'Устройство'),
            'measureType' => Yii::t('app', 'Тип измерения'),
            'measureTypeUuid' => Yii::t('app', 'Тип измерения'),
            'register' => Yii::t('app', 'Регистр'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMeasureType() {
        return $this->hasOne(MeasureType::class, ['uuid' => 'measureTypeUuid']);
    }

    /**
     * @return ActiveQuery
     */
    public function getDevice() {
        return $this->hasOne(Device::class, ['uuid' => 'deviceUuid']);
    }

}
