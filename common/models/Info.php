<?php
namespace common\models;

use common\components\IPhoto;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "info".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $date
 * @property string $log
 * @property string $time
 * @property string $linux
 * @property string $hardware
 * @property string $base_name
 * @property string $software
 * @property string $ip
 * @property string $createdAt
 * @property string $changedAt
 */

class Info extends ActiveRecord
{
    /**
     * Behaviors
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
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'info';
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['uuid', 'date', 'log', 'time', 'linux', 'hardware', 'base_name', 'software', 'ip'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe'],
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'date' => Yii::t('app', 'Дата'),
            'log' => Yii::t('app', 'Лог'),
            'time' => Yii::t('app', 'Время'),
            'linux' => Yii::t('app', 'Версия'),
            'hardware' => Yii::t('app', 'Архитектура'),
            'base_name' => Yii::t('app', 'Версия базы'),
            'software' => Yii::t('app', 'Версия ПО'),
            'ip' => Yii::t('app', 'IP'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id','uuid',
            'date',
            'log',
            'time',
            'linux',
            'hardware',
            'base_name',
            'software',
            'ip',
            'createdAt',
            'changedAt',
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
