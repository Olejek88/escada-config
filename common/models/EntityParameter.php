<?php

namespace common\models;

use common\components\MtmActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "entity_parameter".
 *
 * @property int $_id
 * @property string $uuid
 * @property string $entityUuid
 * @property string $parameter
 * @property string $value
 * @property string $createdAt
 * @property string $changedAt
 */
class EntityParameter extends MtmActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%entity_parameter}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => function () {
                    return $this->scenario == self::SCENARIO_CUSTOM_UPDATE ? $this->changedAt : new Expression('NOW()');
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'entityUuid',
                    'parameter',
                ],
                'required'
            ],
            [
                [
                    'uuid',
                    'entityUuid',
                ],
                'string', 'max' => 36
            ],
            [['uuid'], 'unique'],
            [['parameter'], 'string'],
            [['value'], 'string'],
            [['createdAt', 'changedAt'], 'safe'],
            [['changedAt'], 'string', 'on' => self::SCENARIO_CUSTOM_UPDATE],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'Id',
            'uuid' => 'Uuid',
            'entityUuid' => 'Uuid сущности',
            'parameter' => 'Название параметра',
            'value' => 'Значение параметра',
            'createdAt' => 'Created At',
            'changedAt' => 'Changed At',
        ];
    }
}
