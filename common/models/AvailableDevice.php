<?php

namespace common\models;

use common\components\MtmActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "available_device".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $macAddress
 * @property string $parentMacAddress
 * @property string $shortAddress
 * @property string $createdAt
 * @property string $changedAt
 */
class AvailableDevice extends MtmActiveRecord
{

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'available_device';
    }

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
                    'macAddress',
                    'parentMacAddress',
                    'shortAddress',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                ],
                'string', 'max' => 36,
            ],
            [
                [
                    'macAddress',
                    'parentMacAddress',
                ],
                'string', 'max' => 16,
            ],
            [
                [
                    'shortAddress',
                ],
                'string', 'max' => 4,
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
            'macAddress' => Yii::t('app', 'MAC адрес'),
            'parentMacAddress' => Yii::t('app', 'Родительский MAC адрес'),
            'shortAddress' => Yii::t('app', 'Короткий адрес'),
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
}
