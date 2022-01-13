<?php

namespace common\models;

use common\components\MtmActiveRecord;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "lost_light".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $date
 * @property string $title
 * @property string $status
 * @property string $macAddress
 * @property string $deviceUuid
 * @property string $nodeUuid
 * @property string $createdAt
 * @property string $changedAt
 */
class LostLight extends MtmActiveRecord
{

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'lost_light';
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
                    'date',
                    'title',
                    'status',
                    'macAddress',
                    'deviceUuid',
                    'nodeUuid',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'deviceUuid',
                    'nodeUuid',
                ],
                'string', 'max' => 36,
            ],
            [
                [
                    'title',
                    'macAddress',
                ],
                'string', 'max' => 150,
            ],
            [
                [
                    'date',
                    'status',
                ],
                'string', 'max' => 64,
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
            'date' => Yii::t('app', 'Дата'),
            'title' => Yii::t('app', 'Название'),
            'status' => Yii::t('app', 'Статус'),
            'macAddress' => Yii::t('app', 'MAC адрес'),
            'deviceUuid' => Yii::t('app', 'UUID оборудования'),
            'nodeUuid' => Yii::t('app', 'UUID шкафа установки'),
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
