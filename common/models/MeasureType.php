<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "measure_type"
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class MeasureType extends ActiveRecord
{
    const POWER = '7BDB38C7-EF93-49D4-8FE3-89F2A2AEDB48';
    const TEMPERATURE = '54051538-38F7-44A3-A9B5-C8B5CD4A2936';
    const VOLTAGE = '29A52371-E9EC-4D1F-8BCB-80F489A96DD3';
    const FREQUENCY = '041DED21-D211-4C0B-BCD6-02E392654332';
    const CURRENT = 'E38C561F-9E88-407E-A465-83803A625627';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'measure_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
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
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }
}
