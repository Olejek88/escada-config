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
    const NONE = "E9ADE49A-3C31-42F8-A751-AAEB890C2190";
    const FREQUENCY = "481C2E40-421E-41AB-8BC1-5FB0D01A4CC3";
    const VOLTAGE = "1BEC4685-466F-4AA6-95FC-A3C01BAF09FE";
    const PRESSURE = "69A71072-7EDD-4FF9-B095-0EF145286D79";
    const PHOTO = "8EB1CC6A-FBD5-4A4E-91EE-CA762B94473C";

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
