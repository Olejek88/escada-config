<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "sound_file".
 *
 * @property int $_id Id
 * @property string $entityName
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 */
class LastUpdate extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%last_update}}';
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entityName', 'date'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['entityName'], 'string', 'max' => 50],
            [['entityName'], 'unique'],
            [['date'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'entityName' => 'Название',
            'date' => 'Дата',
            'createdAt' => 'Создан',
            'changedAt' => 'Изменён',
        ];
    }

}
