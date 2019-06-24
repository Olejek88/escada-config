<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "light_answer".
 *
 * @property integer $_id
 * @property string $address
 * @property string $data
 * @property string $dateIn
 * @property string $dateOut
 * @property string $createdAt
 * @property string $changedAt
 */
class LightAnswer extends ActiveRecord
{

    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%light_answer}}';
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
        ];
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

}
